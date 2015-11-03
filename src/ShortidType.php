<?php

namespace PUGX\Shortid\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use PUGX\Shortid\Shortid;

/**
 * Field type mapping for the Doctrine Database Abstraction Layer (DBAL).
 *
 * ShortId fields will be stored as a string in the database and converted back to
 * the ShortId value object when querying.
 */
class ShortidType extends Type
{
    const NAME = 'shortid';

    /**
     * {@inheritdoc}
     *
     * @param array                                     $fieldDeclaration
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $field = ['length' => 7, 'fixed' => true, 'collation' => 'utf8_bin'];

        return $platform->getVarcharTypeDeclarationSQL($field). ' '.$platform->getColumnCollationDeclarationSQL('utf8_bin');
    }

    /**
     * {@inheritdoc}
     *
     * @param string|null                               $value
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof ShortId) {
            return $value;
        }

        return (string) $value;
    }

    /**
     * {@inheritdoc}
     *
     * @param ShortId|null                              $value
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof ShortId || ShortId::isValid($value)) {
            return (string) $value;
        }

        throw ConversionException::conversionFailed($value, self::NAME);
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     *
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     * @return boolean
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return false;
    }
}