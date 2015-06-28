package uk.gov.dvsa.ui.pages;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import uk.gov.dvsa.domain.model.Vehicle;
import uk.gov.dvsa.framework.config.webdriver.MotAppDriver;
import uk.gov.dvsa.helper.PageInteractionHelper;

public class VehicleSearchPage extends Page {

    public static final String path = "/vehicle-search";
    private static final String PAGE_TITLE = "Vehicle search";

    @FindBy(id = "vin-info") private WebElement vinInfo;

    @FindBy(id = "main-message") private WebElement mainMessage;

    @FindBy(id = "additional-message") private WebElement additionalMessage;

    @FindBy(name = "registration") protected WebElement registrationField;

    @FindBy(name = "vin") private WebElement vinField;

    @FindBy(id = "cancel_vehicle_search") private WebElement cancelButton;

    @FindBy(id = "vehicle-search") private WebElement searchButton;

    @FindBy(id = "global-breadcrumb") private WebElement stepInfo;

    @FindBy(id = "vin-type-select") private WebElement vinTypeSelect;

    @FindBy(id = "VehicleSearch") private WebElement vehicleSearchForm;

    @FindBy(id = "new-vehicle-record-link") private WebElement createNewVehicleLink;

    @FindBy(xpath = ".//p[contains(., 'No matches were found for VIN')]") private WebElement
            noVinMatchErrorBox;

    @FindBy(id= "results-table") private WebElement vehicleInfoTable;

    @FindBy(id = "new-vehicle-record-info" ) private WebElement createNewVehicleInfo;

    private By searchResultsTable = By.cssSelector("#results-table a");

    public VehicleSearchPage(MotAppDriver driver) {
        super(driver);
        selfVerify();
    }

    @Override
    protected boolean selfVerify() {
        return PageInteractionHelper.verifyTitle(this.getTitle(), PAGE_TITLE);
    }

    public VehicleSearchPage searchVehicle(Vehicle vehicle){
        registrationField.sendKeys(vehicle.getRegistrationNumber());
        vinField.sendKeys(vehicle.getVin());
        searchButton.click();

        return this;
    }

    public StartTestConfirmationPage selectVehicleFromTable(){
        WebElement vehicleLink = driver.findElement(searchResultsTable);
        vehicleLink.click();

        return new StartTestConfirmationPage(driver);
    }
}