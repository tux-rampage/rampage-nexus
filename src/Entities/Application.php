<?php
/**
 * This is part of rampage-nexus
 * Copyright (c) 2013 Axel Helmert
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    Axel Helmert
 * @copyright Copyright (c) 2013 Axel Helmert
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License
 */

namespace Rampage\Nexus\Entities;

use Rampage\Nexus\Package\PackageInterface;
use Zend\Stdlib\Parameters;
use Psr\Http\Message\StreamInterface;


/**
 * Represents a deployable application
 *
 * This is a logical grouping of all packages of a specific application.
 * It may never contain packages of other applications and it might not exist without at least one
 * Package instance
 */
class Application implements Api\ArrayExchangeInterface
{
    /**
     * The identifier
     *
     * This is the package name that groups all packages
     *
     * @var string
     */
    private $id = null;

    /**
     * The application label
     *
     * This may be the identifier (which is the package name) by default.
     *
     * @var string
     */
    protected $label = null;

    /**
     * Represents the icon as binary data
     *
     * @var StreamInterface
     */
    protected $icon = null;

    /**
     * @var ApplicationPackage[]
     */
    protected $packages = [];

    /**
     * Returns the unique identifier of this application
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the application label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Returns the application name
     * @deprecated 0.9.0
     * @see getLabel() Use `getLabel()` instead
     * @return string
     */
    public function getName()
    {
        return $this->label;
    }

    /**
     * @return StreamInterface
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param StreamInterface $icon
     * @return self
     */
    public function setIcon(StreamInterface $icon)
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @return ApplicationPackage[]
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * Find a package by id
     *
     * @param string $packageId
     * @return PackageInterface
     */
    public function findPackage($packageId)
    {
        foreach ($this->packages as $package) {
            if ($package->getId() == $packageId) {
                return $package;
            }
        }

        return null;
    }

    /**
     * Check if this application has the requested package
     *
     * @param PackageInterface $package
     * @return bool
     */
    public function hasPackage(PackageInterface $package)
    {
        return (isset($this->packages[$package->getId()]));
    }

    /**
     * Exchange entity data with the given array
     *
     * @param array $array
     * @return self
     */
    public function exchangeArray(array $array)
    {
        $params = new Parameters($array);
        $this->label = $params->get('label', $this->label);

        return $this;
    }

    /**
     * Returns the array representation
     *
     * @return array
     */
    public function toArray($withPackages = true)
    {
        $array = [
            'id' => $this->id,
            'label' => $this->label,
        ];

        if (!$withPackages) {
            return $array;
        }

        $array['packages'] = [];

        foreach ($this->packages as $package) {
            $array['packages'][] = $package->getId();
        }

        return $array;
    }
}
