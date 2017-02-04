<?php

namespace Dashboard\ViewModel;

class DashboardViewModel
{
    /** @var HeroActionViewModel $heroActionViewModel */
    private $heroActionViewModel;

    /** @var NotificationsViewModel $notificationsViewModel */
    private $notificationsViewModel;

    /** @var DemoTestViewModel $demoTestViewModel */
    private $demoTestViewModel;

    /**
     * DashboardViewModel constructor.
     *
     * @param HeroActionViewModel    $heroActionViewModel
     * @param NotificationsViewModel $notificationsViewModel
     * @param DemoTestViewModel      $demoTestViewModel
     */
    public function __construct(
        HeroActionViewModel $heroActionViewModel,
        NotificationsViewModel $notificationsViewModel,
        DemoTestViewModel $demoTestViewModel
    ) {
        $this->heroActionViewModel = $heroActionViewModel;
        $this->notificationsViewModel = $notificationsViewModel;
        $this->demoTestViewModel = $demoTestViewModel;
    }

    /**
     * @return HeroActionViewModel
     */
    public function getHeroActionViewModel()
    {
        return $this->heroActionViewModel;
    }

    /**
     * @return DemoTestViewModel
     */
    public function getDemoTestViewModel()
    {
        return $this->demoTestViewModel;
    }

    /**
     * @return NotificationsViewModel
     */
    public function getNotificationsViewModel()
    {
        return $this->notificationsViewModel;
    }
}