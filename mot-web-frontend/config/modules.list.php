<?php
return [
    'DvsaApplicationLogger', // needs to load first to register as exception handler
    'Core',
    'DvsaCommon',
    'Application',
    'Csrf',
    'DvsaFeature',
    'Dvsa\Mot\Frontend\AuthenticationModule',
    'Dvsa\Mot\Frontend\RegistrationModule',
    'Dashboard',
    'UserAdmin',
    'DvsaClient',
    'Site',
    'Equipment',
    'Session',
    'Dvsa\TransitionGroup',
    'Vehicle',
    'Account',
    'Event',
    'DvsaLogger',
    'MaglMarkdown',
    'Soflomo\Purifier',
    'CpmsClient',
    'CpmsForm',
    'SlotPurchase',
    'SlotFinance',
    'Organisation',
    'Report',
    'Dvsa\OpenAM',
    'DoctrineModule',
    'DvsaDoctrineModule',
];