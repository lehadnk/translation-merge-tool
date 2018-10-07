<?php

/*
 * Copyright (c) 2015 Anuj Nair
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpJsonMarshallerTests\ExampleClass;

use PhpJsonMarshaller\Annotations\MarshallProperty;

class MethodMissing
{

    /**
     * @var int $id
     */
    protected $id;

    /**
     * @return int
     * @MarshallProperty(type="int")
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @MarshallProperty(name="id")
     */
    public function setId($id)
    {
        $this->id = $id;
    }

}
