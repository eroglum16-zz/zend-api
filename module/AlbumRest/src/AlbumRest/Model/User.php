<?php

namespace AlbumRest\Model;

use DomainException;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToInt;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\StringLength;

class User implements InputFilterAwareInterface
{
    public $user;
    public $pass;


    public function exchangeArray(array $data)
    {
        $this->user = !empty($data['user']) ? $data['user'] : null;
        $this->pass  = !empty($data['pass']) ? $data['pass'] : null;
    }

    public function getArrayCopy()
    {
        return [
            'user' => $this->user,
            'pass'  => $this->pass,
        ];
    }

}

?>