<?php
/**
 * This file is part of the DVSA MOT Frontend project.
 *
 * @link http://gitlab.clb.npm/mot/mot
 */

namespace Dvsa\Mot\Frontend\PersonModule\View;

use Core\ViewModel\Sidebar\GeneralSidebar;
use Core\ViewModel\Sidebar\GeneralSidebarItemInterface;
use Core\ViewModel\Sidebar\GeneralSidebarLink;
use Core\ViewModel\Sidebar\GeneralSidebarLinkList;
use Core\ViewModel\Sidebar\GeneralSidebarStatusBox;
use Core\ViewModel\Sidebar\GeneralSidebarStatusItem;
use Core\ViewModel\Sidebar\SidebarBadge;
use Dvsa\Mot\Frontend\PersonModule\Security\PersonProfileGuard;
use DvsaClient\Entity\TesterAuthorisation;
use DvsaCommon\Enum\AuthorisationForTestingMotStatusCode;
use DvsaCommon\UrlBuilder\PersonUrlBuilderWeb;

/**
 * PersonProfile Sidebar.
 */
class PersonProfileSidebar extends GeneralSidebar
{
    const OLD_USER_PROFILE_URL = '/profile/';
    const NEW_USER_PROFILE_URL = '/your-profile/';
    const OLD_USER_ADMIN_PROFILE_URL = 'user-admin/user-profile/';
    const NEW_USER_ADMIN_PROFILE_URL = 'user-admin/user/';

    /**
     * @var int
     */
    private $personId;

    /**
     * @var PersonProfileGuard
     */
    private $personProfileGuard;

    /**
     * @var TesterAuthorisation
     */
    private $testerAuthorisation;

    /**
     * @var bool
     */
    private $newProfileEnabled;

    /**
     * @var string
     */
    private $currentUrl;

    /**
     * @param int                 $personId
     * @param PersonProfileGuard  $personProfileGuard
     * @param TesterAuthorisation $testerAuthorisation
     * @param bool                $newProfileEnabled
     * @param string              $currentUrl
     */
    public function __construct(
        $personId,
        PersonProfileGuard $personProfileGuard,
        TesterAuthorisation $testerAuthorisation,
        $newProfileEnabled,
        $currentUrl
    ) {
        $this->personId = $personId;
        $this->personProfileGuard = $personProfileGuard;
        $this->testerAuthorisation = $testerAuthorisation;
        $this->newProfileEnabled = $newProfileEnabled;
        $this->currentUrl = $currentUrl;

        $this->setUpStatusBox();
        $this->setUpAccountSecurityBox();
        $this->setUpAccountManagementBox();
        $this->setUpRelatedLinksSection();
    }

    /**
     * @param string $id of sidebar item to check for
     *
     * @return bool
     */
    public function hasItem($id)
    {
        $getId = function (GeneralSidebarItemInterface $item) {
            return $item->getId();
        };

        return in_array($id, array_map($getId, $this->getSidebarItems()));
    }

    /**
     * @param $code
     *
     * @return SidebarBadge CSS class modifier for status badge
     */
    private function getQualificationStatusModifier($code)
    {
        switch ($code) {
            case AuthorisationForTestingMotStatusCode::INITIAL_TRAINING_NEEDED:
                return SidebarBadge::normal();
                break;
            case AuthorisationForTestingMotStatusCode::DEMO_TEST_NEEDED:
                return SidebarBadge::warning();
                break;
            case AuthorisationForTestingMotStatusCode::QUALIFIED:
                return SidebarBadge::success();
                break;
            case AuthorisationForTestingMotStatusCode::SUSPENDED:
                return SidebarBadge::alert();
                break;
            default :
                return SidebarBadge::normal();
        }
    }

    private function setUpStatusBox()
    {
        $statusBox = new GeneralSidebarStatusBox();
        $statusBox->setId('qualification_status');

        if (true === $this->personProfileGuard->shouldDisplayTesterQualificationStatusBox()) {
            $statusBox->addItem(new GeneralSidebarStatusItem(
                "group-a",
                "Group A",
                $this->testerAuthorisation->getGroupAStatus()->getName(),
                $this->getQualificationStatusModifier($this->testerAuthorisation->getGroupAStatus()->getCode()),
                "Class 1 and 2"));
            $statusBox->addItem(new GeneralSidebarStatusItem(
                "group-b",
                "Group B",
                $this->testerAuthorisation->getGroupBStatus()->getName(),
                $this->getQualificationStatusModifier($this->testerAuthorisation->getGroupBStatus()->getCode()),
                "Class 3, 4, 5 and 7"));
        }

        $this->addItem($statusBox);
    }

    private function setUpAccountSecurityBox()
    {
        if (!$this->personProfileGuard->canViewAccountSecurity()) {
            return;
        }

        $changePasswordUrl = ($this->newProfileEnabled ? sprintf('%s%d/', self::NEW_USER_PROFILE_URL, $this->personId)
                : self::OLD_USER_PROFILE_URL) . 'change-password';
        $resetPinUrl = $this->newProfileEnabled ? sprintf('%s%d/security-question', self::NEW_USER_PROFILE_URL, $this->personId)
            : PersonUrlBuilderWeb::securityQuestions();

        $accountSecurityBox = new GeneralSidebarLinkList('Account security');
        $accountSecurityBox->setId('account_security');
        $accountSecurityBox->addLink(new GeneralSidebarLink('change-password', 'Change your password', $changePasswordUrl));
        $accountSecurityBox->addLink(new GeneralSidebarLink('reset-pin', 'Reset your PIN', $resetPinUrl));

        $this->addItem($accountSecurityBox);
    }

    private function setUpAccountManagementBox()
    {
        if (!$this->personProfileGuard->canViewAccountManagement()) {
            return;
        }

        $userAdminUrl = $this->getUserAdminUrl();

        // without the /, the url will get appended to the current url instead
        // of being appended to the root url
        $usernameRecoveryUrl = '/' . (
            $this->newProfileEnabled ?
                sprintf('%s%d/', $userAdminUrl, $this->personId) :
                self::OLD_USER_PROFILE_URL
            ) . 'username-recover/';

        $accountManagementBox = new GeneralSidebarLinkList('Account management');
        $accountManagementBox->setId('account_management');

        if (true === $this->personProfileGuard->canResetAccount()) {
            $accountManagementBox->addLink(
                new GeneralSidebarLink(
                    'reset-by-email',
                    'Reset account by email',
                    '/' . $userAdminUrl . $this->personId . '/claim-reset',
                    'related-button--warning'
                )
            );
            $accountManagementBox->addLink(
                new GeneralSidebarLink(
                    'reset-by-post',
                    'Reset account by post',
                    '/' . $userAdminUrl . $this->personId . '/password-reset', '', 'or '
                )
            );
        }

        if (true === $this->personProfileGuard->canSendUserIdByPost()) {
            $accountManagementBox->addLink(
                new GeneralSidebarLink(
                    'id-by-post',
                    'Send User ID by post',
                    '/' . $userAdminUrl . $this->personId . '/username-recover'
                )
            );
        }

        if (true === $this->personProfileGuard->canSendPasswordResetByPost()) {
            $accountManagementBox->addLink(
                new GeneralSidebarLink(
                    'password-by-post',
                    'Send password reset by post',
                    '/' . $userAdminUrl . $this->personId . '/claim-reset/post'
                )
            );
        }

        $this->addItem($accountManagementBox);
    }

    private function setUpRelatedLinksSection()
    {
        $changeQualificationStatusUrl = ($this->newProfileEnabled ?
                $this->currentUrl :
                self::OLD_USER_PROFILE_URL . $this->personId) . '/change-qualification-status/';
        $changeGroupAQualificationUrl = $changeQualificationStatusUrl . 'A';
        $changeGroupBQualificationUrl = $changeQualificationStatusUrl . 'B';

        $rolesAndAssociationsUrl = ($this->newProfileEnabled ?
            $this->currentUrl :
            self::OLD_USER_PROFILE_URL . $this->personId) . '/trade-roles';

        $internalRolesUrl = ($this->newProfileEnabled ?
            $this->currentUrl :
            self::OLD_USER_PROFILE_URL . $this->personId) . '/manage-internal-role';

        $relatedBox = new GeneralSidebarLinkList('Related');
        $relatedBox->setId('related');

        if (true === $this->personProfileGuard->canViewTradeRoles()) {
            $relatedBox->addLink(
                new GeneralSidebarLink(
                    'roles-and-associations',
                    'Roles and associations',
                    $rolesAndAssociationsUrl
                )
            );
        }

        if (true === $this->personProfileGuard->canManageDvsaRoles()) {
            $relatedBox->addLink(
                new GeneralSidebarLink(
                    'manage-roles',
                    'Manage roles',
                    $internalRolesUrl
                )
            );
        }

        if (true === $this->personProfileGuard->canChangeTesterQualificationStatus()) {
            $relatedBox->addLink(
                new GeneralSidebarLink(
                    'change-group-a-qualification',
                    'Change Group A qualification status',
                    $changeGroupAQualificationUrl
                )
            );
            $relatedBox->addLink(
                new GeneralSidebarLink(
                    'change-group-b-qualification',
                    'Change Group B qualification status',
                    $changeGroupBQualificationUrl
                )
            );
        }

        if (true === $this->personProfileGuard->canViewEventHistory()) {
            $relatedBox->addLink(
                new GeneralSidebarLink(
                    'event-history',
                    'Event history',
                    '/event/list/person/' . $this->personId . '?previousRoute=' . $this->currentUrl
                )
            );
        }

        if (!empty($relatedBox->getLinks())) {
            $this->addItem($relatedBox);
        }
    }

    /**
     * @return string
     */
    private function getUserAdminUrl()
    {
        return $this->newProfileEnabled ?
            self::NEW_USER_ADMIN_PROFILE_URL :
            self::OLD_USER_ADMIN_PROFILE_URL;
    }

    /**
     * @return string
     */
    private function getUserProfileUrl()
    {
        return $this->newProfileEnabled ? self::NEW_USER_PROFILE_URL : self::OLD_USER_PROFILE_URL;
    }
}
