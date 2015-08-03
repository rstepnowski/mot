package uk.gov.dvsa.ui.pages;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import uk.gov.dvsa.framework.config.webdriver.MotAppDriver;
import uk.gov.dvsa.helper.*;

import static org.hamcrest.MatcherAssert.assertThat;
import static org.hamcrest.core.IsEqual.equalTo;

public class CreateAePage extends Page {
    public static final String path = "/authorised-examiner/create";
    private static final String PAGE_TITLE = "Create Authorised Examiner";

    @FindBy(id = "organisationName") private WebElement businessName;
    @FindBy(id = "tradingAs") private WebElement tradingAs;
    @FindBy(id = "companyType") private WebElement companyType;
    @FindBy(id = "registeredCompanyNumber") private WebElement registeredCompanyNumber;
    @FindBy(id = "REGCaddressLine1") private WebElement regAddressLine1;
    @FindBy(id = "REGCaddressLine2") private WebElement regAddressLine2;
    @FindBy(id = "REGCaddressLine3") private WebElement regAddressLine3;
    @FindBy(id = "REGCaddressTown") private WebElement regAddressTown;
    @FindBy(id = "REGCaddressPostCode") private WebElement regAddressPostCode;
    @FindBy(id = "REGCphoneNumber") private WebElement regPhoneNumber;
    @FindBy(id = "REGCemail") private WebElement regEmail;
    @FindBy(id = "REGCemailConfirmation") private WebElement regEmailConfirmation;
    @FindBy(id = "REGC[isEmailNotSupply]1") private WebElement regEmailSupply;

    @FindBy(id = "isCorrDetailsSame1") private WebElement isCorrDetailsSame1;
    @FindBy(id = "isCorrDetailsSame0") private WebElement isCorrDetailsSame0;

    @FindBy(id = "CORRaddressLine1") private WebElement corrAddressLine1;
    @FindBy(id = "CORRaddressLine2") private WebElement corrAddressLine2;
    @FindBy(id = "CORRaddressLine3") private WebElement corrAddressLine3;
    @FindBy(id = "CORRaddressTown") private WebElement corrAddressTown;
    @FindBy(id = "CORRaddressPostCode") private WebElement corrAddressPostCode;
    @FindBy(id = "CORRphoneNumber") private WebElement corrPhoneNumber;
    @FindBy(id = "CORRemail") private WebElement corrEmail;
    @FindBy(id = "CORRemailConfirmation") private WebElement corrEmailConfirmation;
    @FindBy(id = "CORR[isEmailNotSupply]1") private WebElement corrEmailSupply;

    @FindBy(id = "submitAeEdit") private WebElement continueToSummary;
    @FindBy(id = "navigation-link-") private WebElement cancelAndReturnHome;

    public CreateAePage(MotAppDriver driver) {
        super(driver);
        selfVerify();
    }

    @Override protected boolean selfVerify() {
        return PageInteractionHelper.verifyTitle(this.getTitle(), PAGE_TITLE);
    }

    public CreateAePage enterBusinessDetails() {
        return editBusinessName(CompanyDetailsHelper.businessName).editTradingName(CompanyDetailsHelper.tradingName)
                .selectBusinessType(CompanyDetailsHelper.businessType).editCompanyNumber(
                        CompanyDetailsHelper.companyNumber);
    }

    public CreateAePage enterBusinessAddress() {
        return editBusinessAddressLine1(ContactDetailsHelper.addressLine1).editBusinessAddressLine2(ContactDetailsHelper.addressLine2)
                .editBusinessAddressLine3(ContactDetailsHelper.addressLine3).editBusinessCity(
                        ContactDetailsHelper.city)
                .editBusinessPostCode(ContactDetailsHelper.postCode).editBusinessPhoneNumber(
                        ContactDetailsHelper.phoneNumber)
                .enterBusinessEmail(ContactDetailsHelper.email);
    }

    public CreateAePage enterBusinessEmail(String email) {
        return editBusinessEmail(email).editBusinessEmailConfirmation(email);
    }

    public CreateAePage enterCorrespondenceAddress() {
        return editCorrespondenceAddressLine1(ContactDetailsHelper.addressLine1).editCorrespondenceAddressLine2(
                ContactDetailsHelper.addressLine2)
                .editCorrespondenceAddressLine3(ContactDetailsHelper.addressLine3).editCorrAddressTown(
                        ContactDetailsHelper.city)
                .editCorrespondenceAddressPostCode(ContactDetailsHelper.postCode).editCorrespondencePhoneNumber(
                        ContactDetailsHelper.phoneNumber)
                .enterCorrespondenceEmail(ContactDetailsHelper.email);
    }

    public boolean verifyCorrespondenceDetails() {
        assertThat(getCorrespondenceAddressLine1(), equalTo(ContactDetailsHelper.addressLine1));
        assertThat(getCorrespondenceAddressLine2(), equalTo(ContactDetailsHelper.addressLine2));
        assertThat(getCorrespondenceAddressLine3(), equalTo(ContactDetailsHelper.addressLine3));
        assertThat(getCorrespondenceTown(), equalTo(ContactDetailsHelper.city));
        assertThat(getCorrespondencePostCode(), equalTo(ContactDetailsHelper.postCode));
        assertThat(getCorrespondencePhoneNumber(), equalTo(ContactDetailsHelper.phoneNumber));
        assertThat(getCorrespondenceEmailAddress(), equalTo(ContactDetailsHelper.email));
        assertThat(getCorrespondenceEmailConfirmAddress(), equalTo(ContactDetailsHelper.email));
        return true;
    }

    public CreateAePage completeBusinessAndCorrespondenceDetails(boolean useBusinessDetailsForCorrespondence){
        enterBusinessDetails();
        enterBusinessAddress();

        if(useBusinessDetailsForCorrespondence){
            selectBusinessDetailsSameAsCorrespondenceDetails(useBusinessDetailsForCorrespondence);
            enterCorrespondenceAddress();
        }

        return this;
    }

    public AreaOfficerAuthorisedExaminerViewPage create(){
        clickContinueToSummary().createNewAe();

        return new AreaOfficerAuthorisedExaminerViewPage(driver);
    }

    public CreateAePage enterCorrespondenceEmail(String email) {
        return editCorrespondenceEmail(email).editCorrespondenceEmailConfirmation(email);
    }

    public CreateAePage editBusinessName(String aeBusinessName) {
        FormCompletionHelper.enterText(businessName, aeBusinessName);
        return this;
    }

    public CreateAePage editTradingName(String aeTradingName) {
        FormCompletionHelper.enterText(tradingAs, aeTradingName);
        return this;
    }

    public CreateAePage selectBusinessType(String aeBusinessType) {
        FormCompletionHelper.selectFromDropDownByVisibleText(companyType, aeBusinessType);
        return this;
    }

    public CreateAePage editCompanyNumber(String aeCompanyNumber) {
        FormCompletionHelper.enterText(registeredCompanyNumber, aeCompanyNumber);
        return this;
    }

    public CreateAePage editBusinessAddressLine1(String addressLine1) {
        FormCompletionHelper.enterText(regAddressLine1, addressLine1);
        return this;
    }

    public CreateAePage editBusinessAddressLine2(String addressLine2) {
        FormCompletionHelper.enterText(regAddressLine2, addressLine2);
        return this;
    }

    public CreateAePage editBusinessAddressLine3(String addressLine3) {
        FormCompletionHelper.enterText(regAddressLine3, addressLine3);
        return this;
    }

    public CreateAePage editBusinessCity(String regCity) {
        FormCompletionHelper.enterText(regAddressTown, regCity);
        return this;
    }

    public CreateAePage editBusinessPostCode(String regPostCode) {
        FormCompletionHelper.enterText(regAddressPostCode, regPostCode);
        return this;
    }

    public CreateAePage editBusinessPhoneNumber(String phoneNumber) {
        FormCompletionHelper.enterText(regPhoneNumber, phoneNumber);
        return this;
    }

    public CreateAePage editBusinessEmail(String email) {
        FormCompletionHelper.enterText(regEmail, email);
        return this;
    }

    public boolean isBusinessEmailFieldEmpty() {
        return regEmail.getText().equals("");
    }

    public CreateAePage editBusinessEmailConfirmation(String email) {
        FormCompletionHelper.enterText(regEmailConfirmation, email);
        return this;
    }

    public boolean isBusinessConfirmationEmailFieldEmpty() {
        return regEmailConfirmation.getText().equals("");
    }

    public boolean isBusinessEmailFieldsEmpty() {
        return isBusinessEmailFieldEmpty() && isBusinessConfirmationEmailFieldEmpty();
    }

    public CreateAePage selectBusinessEmailNotProvidedOption() {
        FormCompletionHelper.selectInputBox(regEmailSupply);
        return this;
    }

    public CreateAePage selectBusinessDetailsSameAsCorrespondenceDetails(boolean value) {
        if(value){
            FormCompletionHelper.selectInputBox(isCorrDetailsSame1);
            return this;
        }

        FormCompletionHelper.selectInputBox(isCorrDetailsSame0);
        return this;
    }

    public CreateAePage editCorrespondenceAddressLine1(String addressLine1) {
        FormCompletionHelper.enterText(corrAddressLine1, addressLine1);
        return this;
    }

    public CreateAePage editCorrespondenceAddressLine2(String addressLine2) {
        FormCompletionHelper.enterText(corrAddressLine2, addressLine2);
        return this;
    }

    public CreateAePage editCorrespondenceAddressLine3(String addressLine3) {
        FormCompletionHelper.enterText(corrAddressLine3, addressLine3);
        return this;
    }

    public CreateAePage editCorrAddressTown(String city) {
        FormCompletionHelper.enterText(corrAddressTown, city);
        return this;
    }

    public CreateAePage editCorrespondenceAddressPostCode(String postCode) {
        FormCompletionHelper.enterText(corrAddressPostCode, postCode);
        return this;
    }

    public CreateAePage editCorrespondencePhoneNumber(String phoneNumber) {
        FormCompletionHelper.enterText(corrPhoneNumber, phoneNumber);
        return this;
    }

    public CreateAePage editCorrespondenceEmail(String email) {
        FormCompletionHelper.enterText(corrEmail, email);
        return this;
    }

    public boolean isCorrespondenceEmailFieldEmpty() {
        return corrEmail.getText().equals("");
    }

    public CreateAePage editCorrespondenceEmailConfirmation(String email) {
        FormCompletionHelper.enterText(corrEmailConfirmation, email);
        return this;
    }

    public boolean isCorrespondenceConfirmationEmailFieldEmpty() {
        return corrEmailConfirmation.getText().equals("");
    }

    public boolean isCorrespondenceEmailFieldsEmpty() {
        return isCorrespondenceEmailFieldEmpty() && isCorrespondenceConfirmationEmailFieldEmpty();
    }

    public CreateAePage selectCorrespondenceEmailNotProvided() {
        FormCompletionHelper.selectInputBox(corrEmailSupply);
        return this;
    }

    public String getCorrespondenceAddressLine1() {
        return corrAddressLine1.getAttribute("value");
    }

    public String getCorrespondenceAddressLine2() {
        return corrAddressLine2.getAttribute("value");
    }

    public String getCorrespondenceAddressLine3() {
        return corrAddressLine3.getAttribute("value");
    }

    public String getCorrespondenceTown() {
        return corrAddressTown.getAttribute("value");
    }

    public String getCorrespondencePostCode() {
        return corrAddressPostCode.getAttribute("value");
    }

    public String getCorrespondencePhoneNumber() {
        return corrPhoneNumber.getAttribute("value");
    }

    public String getCorrespondenceEmailAddress() {
        return corrEmail.getAttribute("value");
    }

    public String getCorrespondenceEmailConfirmAddress() {
        return corrEmailConfirmation.getAttribute("value");
    }

    public ConfirmNewAeDetailsPage clickContinueToSummary() {
        continueToSummary.click();
        return new ConfirmNewAeDetailsPage(driver);
    }
}