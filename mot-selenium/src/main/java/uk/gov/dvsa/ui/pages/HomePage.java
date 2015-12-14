package uk.gov.dvsa.ui.pages;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import uk.gov.dvsa.framework.config.webdriver.MotAppDriver;
import uk.gov.dvsa.helper.PageInteractionHelper;
import uk.gov.dvsa.ui.pages.dvsa.UserSearchPage;
import uk.gov.dvsa.ui.pages.vts.VehicleTestingStationPage;

import java.util.List;

public class HomePage extends Page {

    public static final String path = "/";
    private static final String PAGE_TITLE = "Your home";

    @FindBy (className = "global-header_name") private WebElement userNameHeader;
    @FindBy (css = ".pivot-panel_title a[href*=authorised]") private WebElement aeTitle;
    @FindBy (css = ".pivot-panel_header p") private WebElement aeNumber;
    @FindBy (css = ".pivot-panel_meta-list span") private WebElement roleType;
    @FindBy (css = ".site-link") private WebElement siteName;
    @FindBy (id = "action-resume-mot-test") private WebElement resumeMotTestButton;
    @FindBy (id = "header_title") private WebElement vtsActivityLabel;
    @FindBy (id = "mot-test-certificates-list") private WebElement motCertificateList;
    private By startMotRetest = By.id("action-start-mot-retest");
    @FindBy (id = "action-start-certificate-reissue") private  WebElement StartCertificateReissue;
    @FindBy(className = "notification_link") private WebElement notificationMessage;
    @FindBy(id = "action-start-user-search") private WebElement userSearchLink;

    private static final By ROLE_NOMINATION_LIST = By.cssSelector(".notification_subject > a");

    private List<WebElement> getTesterNominationElements() {
        return driver.findElements(ROLE_NOMINATION_LIST);
    }

    public HomePage(MotAppDriver driver) {
        super(driver);
        selfVerify();
    }

    @Override
    protected boolean selfVerify() {
        String j = driver.getCurrentUser().getNamesAndSurname();

        return PageInteractionHelper.verifyTitle(this.getTitle(), PAGE_TITLE)
                && userNameHeader.getText().equals(driver.getCurrentUser().getNamesAndSurname());
    }

    public HomePage clickOnLastNomination() {
        getTesterNominationElements().get(0).click();
        return this;
    }

    public String getAeName(){
        return aeTitle.getText();
    }

    public String getAeNumber() {
        return aeNumber.getText();
    }

    public String getSiteName(){
        return siteName.getText();
    }

    public String getResumeMotTestButtonText() {
        return resumeMotTestButton.getText();
    }

    public String getRole(){
        return roleType.getText();
    }

    public VehicleTestingStationPage selectRandomVts() {
        siteName.click();

        return new VehicleTestingStationPage(driver);
    }

    public UserSearchPage clickUserSearchLinkExpectingUserSearchPage() {
        userSearchLink.click();
        return new UserSearchPage(driver);
    }

    public void clickEnterTestResultsButton() {
        resumeMotTestButton.click();
    }

    public boolean compareUserNameWithSessionUsername() {
        return userNameHeader.getText().equals(driver.getCurrentUser().getNamesAndSurname());
    }

    public boolean isRetestPreviousVehicleLinkPresent() {
        return PageInteractionHelper.isElementPresent(startMotRetest);
    }

    public boolean isResumeMotTestDisplayed() {
        return PageInteractionHelper.isElementDisplayed(resumeMotTestButton);
    }

    public boolean isNotificationMessageDisplayed() {
        return notificationMessage.isDisplayed();
    }

    public boolean isVtsActivityLabelDisplayed(){
        return vtsActivityLabel.isDisplayed();
    }

    public boolean isMotCertificateListDisplayed(){
        return motCertificateList.isDisplayed();
    }

}
