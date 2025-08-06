<?php
// File: app/Libraries/CustomPager.php

namespace App\Libraries;

use CodeIgniter\Pager\Pager as BasePager;
use Config\Pager as PagerConfig;
use CodeIgniter\View\RendererInterface;
use Config\Services;

class CustomPager extends BasePager
{
    protected $pageCount = 1;
    protected $currentPage = 1;
    protected $perPage = 10;
    protected $total = 0;
    protected $path = '';

    /**
     * Constructor
     */
    public function __construct(PagerConfig $config, RendererInterface $view)
    {
        parent::__construct($config, $view);
    }

    /**
     * Set total pages
     */
    public function setPageCount(int $pageCount): self
    {
        $this->pageCount = max(1, $pageCount);
        return $this;
    }

    /**
     * Set current page
     */
    public function setCurrentPage(int $currentPage): self
    {
        $this->currentPage = max(1, min($currentPage, $this->pageCount));
        return $this;
    }

    /**
     * Set items per page
     */
    public function setPerPage(int $perPage): self
    {
        $this->perPage = max(1, $perPage);
        return $this;
    }

    /**
     * Set total items
     */
    public function setTotal(int $total): self
    {
        $this->total = max(0, $total);
        $this->pageCount = max(1, (int) ceil($this->total / $this->perPage));
        return $this;
    }

    /**
     * Set base path for pagination links
     */
    public function setPath(string $path, string $group = 'default'): self
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Get details about pagination
     */
    public function getDetails(string $group = 'default'): array
    {
        return [
            'pageCount'   => $this->pageCount,
            'currentPage' => $this->currentPage,
            'perPage'     => $this->perPage,
            'total'       => $this->total,
            'path'        => $this->path,
            'group'       => $group,
        ];
    }

    /**
     * Override links method to generate pagination links
     */
    public function links(?string $group = 'default', string $template = 'default_full'): string
    {
        if ($this->pageCount <= 1) {
            return '';
        }

        $this->setPath($this->path, $group);
        
        // Configure the pager
        $this->store($group, $this->currentPage, $this->perPage, $this->total, $this->pageCount);

        return parent::links($group, $template);
    }
}