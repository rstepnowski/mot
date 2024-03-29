<?php

namespace Dvsa\Mot\Api\StatisticsApi\TesterQualityInformation\TesterPerformance\TesterNational\Storage;

use Dvsa\Mot\Api\StatisticsApi\TesterQualityInformation\Common\Storage\S3KeyGenerator;
use DvsaCommon\ApiClient\Statistics\Common\ReportDtoInterface;
use DvsaCommon\ApiClient\Statistics\TesterPerformance\Dto\NationalPerformanceReportDto;
use DvsaCommon\KeyValueStorage\KeyValueStorageInterface;

class NationalTesterPerformanceStatisticsStorage
{
    private $storage;
    private $keyGenerator;

    public function __construct(
        KeyValueStorageInterface $statisticsStorage
    )
    {
        $this->storage = $statisticsStorage;
        $this->keyGenerator = new S3KeyGenerator();
    }

    /**
     * @param $year
     * @param $month
     * @return ReportDtoInterface
     */
    public function get($year, $month)
    {
        $key = $this->keyGenerator->generateForNationalTesterStatistics($year, $month);

        /** @var ReportDtoInterface $reportDto */
        $reportDto = $this->storage->getAsDto($key, NationalPerformanceReportDto::class);

        return $reportDto;
    }

    public function store($year, $month, NationalPerformanceReportDto $data)
    {
        $key = $this->keyGenerator->generateForNationalTesterStatistics($year, $month);

        $this->storage->storeDto($key, $data);
    }
}
