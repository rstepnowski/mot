package uk.gov.dvsa.ui.pages.cpms;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import uk.gov.dvsa.framework.config.webdriver.MotAppDriver;
import uk.gov.dvsa.helper.PageInteractionHelper;
import uk.gov.dvsa.ui.pages.Page;

public class SlotRefundConfirmationPage extends Page {
    private static final String PAGE_TITLE = "Slot refund confirmation";

    @FindBy(id = "successMessage") private WebElement successMessage;

    public SlotRefundConfirmationPage(MotAppDriver driver) {
        super(driver);
        selfVerify();
    }

    @Override
    protected boolean selfVerify() {
        return PageInteractionHelper.verifyTitle(this.getTitle(), PAGE_TITLE);
    }

    public boolean isRefundSuccessMessageDisplayed() {
        return successMessage.isDisplayed();
    }
}
