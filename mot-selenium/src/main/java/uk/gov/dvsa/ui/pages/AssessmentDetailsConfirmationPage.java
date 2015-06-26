package uk.gov.dvsa.ui.pages;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;
import uk.gov.dvsa.framework.config.webdriver.MotAppDriver;
import uk.gov.dvsa.helper.PageInteractionHelper;

public class AssessmentDetailsConfirmationPage extends Page {

    private static final String PAGE_TITLE = "Assessment details saved";

    @FindBy (id = "validation-message--success") private WebElement validationMsgSuccess;
    private MotAppDriver driver;

    public AssessmentDetailsConfirmationPage(MotAppDriver driver) {
        super(driver);
        selfVerify();
        this.driver = driver;
        PageFactory.initElements(driver, this);
    }

    @Override
    protected boolean selfVerify() {
        return PageInteractionHelper.verifyTitle(this.getTitle(), PAGE_TITLE);
    }

    public boolean isValidationMsgSuccess() {
        return validationMsgSuccess.isDisplayed();
    }
}
