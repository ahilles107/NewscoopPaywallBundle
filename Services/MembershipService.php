<?php
/**
 * @package Newscoop\PaywallBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\PaywallBundle\Services;

use Newscoop\PaywallBundle\Services\PaywallService;
use Newscoop\Services\EmailService;
use Newscoop\Services\UserService;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

/**
 * Membership Service
 */
class MembershipService
{
    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    protected $subscriptionService;

    protected $userService;

    protected $emailService;

    protected $templatesService;

    /** @var Newscoop\Services\PlaceholdersService */
    protected $placeholdersService;

    protected $zendRouter;

    protected $preferencesService;

    /**
     * Construct
     *
     * @param EntityManager  $em                  Entity Manager
     * @param PaywallService $subscriptionService Paywall service
     * @param UserService    $userService         User service
     * @param Zend_Router    $zendRouter          Zend router
     *
     */
    public function __construct(EntityManager $em, PaywallService $subscriptionService, UserService $userService,
        EmailService $emailService, $templatesService, $placeholdersService, $zendRouter, $preferencesService)
    {
        $this->em = $em;
        $this->subscriptionService = $subscriptionService;
        $this->userService = $userService;
        $this->emailService = $emailService;
        $this->templatesService = $templatesService;
        $this->placeholdersService = $placeholdersService;
        $this->zendRouter = $zendRouter;
        $this->preferencesService = $preferencesService;
    }

    public function sendEmail(Request $request, $newSubscriptionName, $currentSubscriptionName, $toPay, $status, $toUser = false, $leftTrialDays = null)
    {
        $user = $this->userService->getCurrentUser();
        $smarty = $this->templatesService->getSmarty();
        $smarty->assign('user', new \MetaUser($user));
        $smarty->assign('customerId', $user->getAttribute('customer_id'));
        $smarty->assign('newSubscriptionName', $newSubscriptionName);
        $smarty->assign('currentSubscriptionName', $currentSubscriptionName);
        $smarty->assign('toPay', $toPay);
        $smarty->assign('status', $status);
        $smarty->assign('street', $user->getStreet());
        $smarty->assign('postal', $user->getPostal());
        $smarty->assign('city', $user->getCity());
        $smarty->assign('state', $user->getState());
        $smarty->assign('leftTrialDays', $leftTrialDays);
        $smarty->assign('userLink', $request->getUriForPath($this->zendRouter->assemble(array('controller' => 'user', 'action' => 'profile')) . '/' . $user->getUsername()));
        if ($toUser) {
            $message = $this->templatesService->fetchTemplate("email_membership_user.tpl");
            $this->emailService->send($this->placeholdersService->get('subject'), $message, array($user->getEmail()));
        }

        $message = $this->templatesService->fetchTemplate("email_membership_staff.tpl");
        $this->emailService->send($this->placeholdersService->get('subject'), $message, array($this->preferencesService->PaywallMembershipNotifyEmail));
    }

    public function expiringSubscriptionNotifyEmail($userSubscription)
    {
        $smarty = $this->templatesService->getSmarty();
        $smarty->assign('user', new \MetaUser($userSubscription->getUser()));
        $smarty->assign('customerId', $userSubscription->getUser()->getAttribute('customer_id'));
        $smarty->assign('subscription', $userSubscription);

        $message = $this->templatesService->fetchTemplate("email_membership_expire.tpl");
        $this->emailService->send($this->placeholdersService->get('subject'), $message, array($userSubscription->getUser()));
    }

    /**
     * Calculates diffrence between subscriptions prices when upgrading/downgrading
     *
     * @return string
     */
    public function calculatePriceDiff($currentSubscription, $newSubscription)
    {
        $currentToPay = (int) $currentSubscription->getToPay();
        $newToPay = (int) $newSubscription->getToPay();

        $sum = 0;
        if ($newToPay > $currentToPay) {
            $sum = $newToPay - $currentToPay;
        }

        return $sum;
    }

    /**
     * Calculates days left to trial expiration when trial is still active
     *
     */
    public function calculateTrialDiff($trialExpireDate)
    {
        $now = new \DateTime();
        $diff = $now->diff($trialExpireDate);

        return $diff->format("%R%a");
    }

    /**
     * Checks if user submitted more than 3 membership requests in a row on the same day (spam protection)
     *
     */
    public function isSpam()
    {
        $now = new \DateTime();
        $user = $this->userService->getCurrentUser();
        $qb = $this->em->getRepository('Newscoop\PaywallBundle\Entity\UserSubscription')
            ->createQueryBuilder('s');

        $qb
            ->select('count(s)')
            ->where('s.user = :user')
            ->andWhere($qb->expr()->gte('s.created_at', ':yesterday'))
            ->andWhere('s.active = :status')
            ->setParameters(array(
                'user' => $user,
                'status' => 'N',
                'yesterday' => $now->modify('-1 day'),
            ))
            ->orderBy('s.created_at', 'desc');

        $subscriptionsCount = (int) $qb->getQuery()->getSingleScalarResult();

        if ($subscriptionsCount > 3) {
            return true;
        }

        return false;
    }

    /**
     * Checks if user details (Address) is filled in
     *
     */
    public function isUserAddressFilledIn($user)
    {
        if (!$user->getFirstName() || !$user->getLastName() || !$user->getPostal() || !$user->getStreet() || !$user->getCity() || !$user->getState()) {
            return false;
        }

        return true;
    }
}
