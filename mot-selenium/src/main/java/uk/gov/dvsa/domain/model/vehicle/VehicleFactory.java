package uk.gov.dvsa.domain.model.vehicle;


import org.apache.commons.lang3.RandomStringUtils;
import org.joda.time.DateTime;
import uk.gov.dvsa.domain.api.response.Vehicle;

public class VehicleFactory {
    public static Vehicle generateValidDetails() {

        String randomRegistrationNumber = RandomStringUtils.randomAlphabetic(7);
        return Vehicle.createVehicle(
                Colours.Blue.getName(),
                CountryOfRegistration.Great_Britain.getRegistrationId(),
                "1700",
                randomRegistrationNumber,
                randomRegistrationNumber,
                new DateTime().minusYears(1).toString(),
                FuelTypes.Diesel.getName(),
                Make.BMW.getName(),
                Model.BMW_ALPINA.getName(),
                Colours.Black.getName(),
                TransmissionType.Manual.getName(),
                RandomStringUtils.randomAlphabetic(17),
                VehicleClass.four,
                "888",
                false
        );
    }

    public static Vehicle generateEmptyAndInvalidDetails() {
        return Vehicle.createVehicle(
                " ", " ", " ",
                " ", " ", " ",
                "Fake name", "Fake make", "Fake model",
                Colours.Black.getName(),
                "FakeFuel",
                " ",
                VehicleClass.four,
                "888",
                false
        );
    }
}
