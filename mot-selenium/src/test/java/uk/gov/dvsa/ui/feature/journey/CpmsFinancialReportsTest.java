package uk.gov.dvsa.ui.feature.journey;

import org.testng.annotations.BeforeClass;
import org.testng.annotations.Test;
import uk.gov.dvsa.domain.model.User;
import uk.gov.dvsa.ui.BaseTest;
import uk.gov.dvsa.ui.pages.cpms.DownloadReportPage;
import uk.gov.dvsa.ui.pages.cpms.GenerateReportPage;

import java.io.IOException;

import static org.hamcrest.MatcherAssert.assertThat;
import static org.hamcrest.core.Is.is;

public class CpmsFinancialReportsTest extends BaseTest {
    
    private User financeUser;
    
    @BeforeClass(alwaysRun = true)
    private void setup() throws IOException {
        financeUser = userData.createAFinanceUser("Finance", false);
    }
    
    @Test (groups = {"BVT", "Regression"}, description = "SPMS-272 User requests Slot Balance report")
    public void userGeneratesReportSuccessfully() throws IOException {
        
        //Given I am on Generate report page
        motUI.cpms.reports.goToGenerateReportPage(financeUser);
        
        //When I select report type and Submit
        motUI.cpms.reports.generateFinancialReport("CPMS82FA1F0C");
        
        //Then The report should be created successfully
        assertThat(motUI.cpms.reports.downloadReportPage.isBackToGenerateReportLinkDisplayed(), is(true));
    }
}
