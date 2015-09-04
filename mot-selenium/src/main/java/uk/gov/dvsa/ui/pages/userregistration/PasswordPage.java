package uk.gov.dvsa.ui.pages.userregistration;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import uk.gov.dvsa.framework.config.webdriver.MotAppDriver;
import uk.gov.dvsa.helper.FormCompletionHelper;
import uk.gov.dvsa.helper.PageInteractionHelper;
import uk.gov.dvsa.ui.pages.Page;

public class PasswordPage extends Page {

    private static final String PAGE_TITLE = "Create a password";

    @FindBy(id = "continue") private WebElement continueToNextPage;

    @FindBy(id = "password") private WebElement createPassword;

    @FindBy(id = "passwordConfirm") private WebElement retypePassword;

    public PasswordPage(MotAppDriver driver){
        super(driver);
        selfVerify();
    }

    @Override
    protected boolean selfVerify() {
        return PageInteractionHelper.verifyTitle(getTitle(), PAGE_TITLE);
    }

    public SummaryPage enterPasswordAndRetypeExpectingSummaryPage(String password, String passwordTwo){
        FormCompletionHelper.enterText(createPassword, password);
        FormCompletionHelper.enterText(retypePassword, passwordTwo);
        continueToNextPage.click();
        return new SummaryPage(driver);
    }

    public SummaryPage clickContinue() {
        continueToNextPage.click();
        return new SummaryPage(driver);
    }

}
