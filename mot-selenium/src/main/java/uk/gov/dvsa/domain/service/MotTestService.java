package uk.gov.dvsa.domain.service;

import com.jayway.restassured.response.Response;
import org.joda.time.DateTime;
import uk.gov.dvsa.domain.api.request.CreateMotTestRequest;
import uk.gov.dvsa.domain.api.request.MotTestData;
import uk.gov.dvsa.domain.model.MotTest;
import uk.gov.dvsa.domain.model.TestOutcome;
import uk.gov.dvsa.domain.model.User;
import uk.gov.dvsa.domain.model.Vehicle;
import uk.gov.dvsa.framework.config.webdriver.WebDriverConfigurator;

import java.io.IOException;


public class MotTestService extends BaseService {
    private static final String CREATE_MOT_TEST_PATH = "/testsupport/mottest";
    private AuthService authService = new AuthService();

    protected MotTestService() {
        super(WebDriverConfigurator.testSupportUrl());
    }

    public MotTest createMotTest(User requestor, int siteId,
                                 Vehicle vehicle, TestOutcome outcome,
                                 int mileage, DateTime issuedDate) throws IOException {

        MotTestData testData = new MotTestData(outcome, mileage, issuedDate);
        return createMotTest(requestor, vehicle, siteId, testData);
    }

    public MotTest createMotTest(User requestor, Vehicle vehicle, int vtsId, MotTestData testData) throws IOException {
        String request =
                jsonHandler.convertToString(new CreateMotTestRequest(requestor, vehicle, vtsId, testData));

        String token = authService.createSessionTokenForUser(requestor);
        Response response = motClient.post(request, CREATE_MOT_TEST_PATH, token);

        return jsonHandler.hydrateObject(
                jsonHandler.convertToString(response.body().path("data")), MotTest.class);
    }

    public MotTest createMotTest(CreateMotTestRequest motTestRequest) throws IOException {
        String request =
                jsonHandler.convertToString(motTestRequest);

        String token = authService.createSessionTokenForUser(
                motTestRequest.requestorName(), motTestRequest.requestorPassword()
        );

        Response response = motClient.post(request, CREATE_MOT_TEST_PATH, token);

        return jsonHandler.hydrateObject(
                jsonHandler.convertToString(response.body().path("data")), MotTest.class);
    }
}
