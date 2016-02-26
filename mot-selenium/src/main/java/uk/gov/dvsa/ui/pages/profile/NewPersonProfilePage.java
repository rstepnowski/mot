package uk.gov.dvsa.ui.pages.profile;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import uk.gov.dvsa.domain.navigation.MotPageFactory;
import uk.gov.dvsa.framework.config.webdriver.MotAppDriver;
import uk.gov.dvsa.helper.PageInteractionHelper;
import uk.gov.dvsa.ui.pages.ChangeEmailDetailsPage;
import uk.gov.dvsa.ui.pages.ChangeTelephoneDetailsPage;
import uk.gov.dvsa.ui.pages.dvsa.RolesAndAssociationsPage;
import uk.gov.dvsa.ui.pages.dvsa.UserSearchResultsPage;
import uk.gov.dvsa.ui.pages.vts.ChangeDetails.ChangeDetailsAddressPage;

public class NewPersonProfilePage extends ProfilePage {

    public static final String PATH = "/your-profile";
    private static final String PAGE_TITLE = "Your profile";

    @FindBy(id = "personal_details") private WebElement personalDetails;
    @FindBy(css = "#date-of-birth a") protected WebElement changeDOBLink;
    @FindBy(css = "#display-name a") private WebElement changeNameLink;
    @FindBy(id = "contact_details") private WebElement contactDetails;
    @FindBy(id = "qualification_status") private WebElement qualificationStatus;
    @FindBy(id = "account_management") private WebElement accountManagement;
    @FindBy(id = "related") private WebElement related;
    @FindBy(id = "account_security") private WebElement accountSecurity;
    @FindBy(id = "roles-and-associations") private WebElement rolesAndAssociationsLink;
    @FindBy(css = "#email-address a") private WebElement changeEmailLink;
    @FindBy(css = "#full-address a") private WebElement changeAddressLink;
    @FindBy(css = "#email-address") private WebElement userEmail;
    @FindBy(css = "#telephone-number") private WebElement userTelephone;
    @FindBy(css = "#telephone-number a") private WebElement userTelephoneLink;
    @FindBy(css = "#validation-message--success") private WebElement successMessage;

    private static String DVSA_ROLES = "dvsa_roles";
    private static String QUALIFICATION_STATUS = "qualification_status";
    private static String GROUP_A_QUALIFICATION = "change-group-a-qualification";
    private static String GROUP_B_QUALIFICATION = "change-group-b-qualification";

    public NewPersonProfilePage(MotAppDriver driver) {
        super(driver);
        selfVerify();
    }

    @Override
    protected boolean selfVerify() {
        return PageInteractionHelper.verifyTitle(this.getTitle(), PAGE_TITLE);
    }

    @Override
    public boolean isChangeDrivingLicenceLinkIsDisplayed() {
        return !personalDetails.findElements(By.xpath(".//*[@id='drivingLicence']/a")).isEmpty();
    }

    @Override
    public boolean isChangeEmailLinkIsDisplayed() {
        return !contactDetails.findElements(By.xpath(".//*[@id='email-address']/a")).isEmpty();
    }

    @Override
    public boolean isSuccessMessageDisplayed() {
        return successMessage.isDisplayed();
    }

    @Override
    public boolean isChangeDOBLinkIsDisplayed() {
        return PageInteractionHelper.isElementDisplayed(changeDOBLink);
    }

    @Override
    public boolean isChangeNameLinkDisplayed() {
        return PageInteractionHelper.isElementDisplayed(changeNameLink);
    }

    @Override
    public boolean isDvsaRolesSectionIsDisplayed() {
        return !driver.findElements(By.id(DVSA_ROLES)).isEmpty();
    }

    public boolean isQualificationStatusSectionIsDisplayed() {
        return PageInteractionHelper.isElementDisplayed(qualificationStatus);
    }

    @Override
    public boolean isDrivingLicenceInformationIsDisplayed() {
        return !personalDetails.findElements(By.id("drivingLicence")).isEmpty();
    }

    @Override
    public boolean isAccountSecuritySectionDisplayed() {
        return accountSecurity.isDisplayed();
    }

    @Override
    public boolean isAccountManagementSectionDisplayed() {
        return accountManagement.isDisplayed();
    }

    @Override
    public boolean isChangeQualificationLinksDisplayed() {
        return !related.findElements(By.id(GROUP_A_QUALIFICATION)).isEmpty() &&
                !related.findElements(By.id(GROUP_B_QUALIFICATION)).isEmpty();
    }

    @Override
    public boolean isTesterQualificationStatusDisplayed() {
        return qualificationStatus.isDisplayed();
    }

    @Override
    public RolesAndAssociationsPage clickRolesAndAssociationsLink() {
        rolesAndAssociationsLink.click();
        return new RolesAndAssociationsPage(driver);
    }

    @Override
    public boolean isRolesAndAssociationsLinkDisplayed() {
        return PageInteractionHelper.isElementDisplayed(rolesAndAssociationsLink);
    }

    @Override
    public ChangeEmailDetailsPage clickChangeEmailLink() {
        changeEmailLink.click();
        return new ChangeEmailDetailsPage(driver);
    }

    public ChangeTelephoneDetailsPage clickChangeTelephoneLink() {
        userTelephoneLink.click();
        return new ChangeTelephoneDetailsPage(driver);
    }

    public boolean verifyEmailIsChanged(String email) {
        return userEmail.getText().contains(email);
    }

    public boolean isPageLoaded() {
        return selfVerify();
    }

    @Override
    public ChangeAddressPage clickChangeAddressLink() {
        changeAddressLink.click();
        return new ChangeAddressPage(driver);
    }
}