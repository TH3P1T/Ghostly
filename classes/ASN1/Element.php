<?php

declare(strict_types = 1);

namespace ASN1;

use ASN1\Component\Identifier;
use ASN1\Component\Length;
use ASN1\Exception\DecodeException;
use ASN1\Feature\ElementBase;
use ASN1\Type\Constructed;
use ASN1\Type\Primitive;
use ASN1\Type\StringType;
use ASN1\Type\TaggedType;
use ASN1\Type\TimeType;
use ASN1\Type\UnspecifiedType;
use ASN1\Type\Tagged\ApplicationType;
use ASN1\Type\Tagged\PrivateType;

/**
 * Base class for all ASN.1 type elements.
 */
abstract class Element implements ElementBase
{
    // Universal type tags
    const TYPE_EOC = 0x00;
    const TYPE_BOOLEAN = 0x01;
    const TYPE_INTEGER = 0x02;
    const TYPE_BIT_STRING = 0x03;
    const TYPE_OCTET_STRING = 0x04;
    const TYPE_NULL = 0x05;
    const TYPE_OBJECT_IDENTIFIER = 0x06;
    const TYPE_OBJECT_DESCRIPTOR = 0x07;
    const TYPE_EXTERNAL = 0x08;
    const TYPE_REAL = 0x09;
    const TYPE_ENUMERATED = 0x0a;
    const TYPE_EMBEDDED_PDV = 0x0b;
    const TYPE_UTF8_STRING = 0x0c;
    const TYPE_RELATIVE_OID = 0x0d;
    const TYPE_SEQUENCE = 0x10;
    const TYPE_SET = 0x11;
    const TYPE_NUMERIC_STRING = 0x12;
    const TYPE_PRINTABLE_STRING = 0x13;
    const TYPE_T61_STRING = 0x14;
    const TYPE_VIDEOTEX_STRING = 0x15;
    const TYPE_IA5_STRING = 0x16;
    const TYPE_UTC_TIME = 0x17;
    const TYPE_GENERALIZED_TIME = 0x18;
    const TYPE_GRAPHIC_STRING = 0x19;
    const TYPE_VISIBLE_STRING = 0x1a;
    const TYPE_GENERAL_STRING = 0x1b;
    const TYPE_UNIVERSAL_STRING = 0x1c;
    const TYPE_CHARACTER_STRING = 0x1d;
    const TYPE_BMP_STRING = 0x1e;
    
    /**
     * Mapping from universal type tag to implementation class name.
     *
     * @internal
     *
     * @var array
     */
    const MAP_TAG_TO_CLASS = [ /* @formatter:off */
        self::TYPE_BOOLEAN => Primitive\Boolean::class,
        self::TYPE_INTEGER => Primitive\Integer::class,
        self::TYPE_BIT_STRING => Primitive\BitString::class,
        self::TYPE_OCTET_STRING => Primitive\OctetString::class,
        self::TYPE_NULL => Primitive\NullType::class,
        self::TYPE_OBJECT_IDENTIFIER => Primitive\ObjectIdentifier::class,
        self::TYPE_OBJECT_DESCRIPTOR => Primitive\ObjectDescriptor::class,
        self::TYPE_REAL => Primitive\Real::class,
        self::TYPE_ENUMERATED => Primitive\Enumerated::class,
        self::TYPE_UTF8_STRING => Primitive\UTF8String::class,
        self::TYPE_RELATIVE_OID => Primitive\RelativeOID::class,
        self::TYPE_SEQUENCE => Constructed\Sequence::class,
        self::TYPE_SET => Constructed\Set::class,
        self::TYPE_NUMERIC_STRING => Primitive\NumericString::class,
        self::TYPE_PRINTABLE_STRING => Primitive\PrintableString::class,
        self::TYPE_T61_STRING => Primitive\T61String::class,
        self::TYPE_VIDEOTEX_STRING => Primitive\VideotexString::class,
        self::TYPE_IA5_STRING => Primitive\IA5String::class,
        self::TYPE_UTC_TIME => Primitive\UTCTime::class,
        self::TYPE_GENERALIZED_TIME => Primitive\GeneralizedTime::class,
        self::TYPE_GRAPHIC_STRING => Primitive\GraphicString::class,
        self::TYPE_VISIBLE_STRING => Primitive\VisibleString::class,
        self::TYPE_GENERAL_STRING => Primitive\GeneralString::class,
        self::TYPE_UNIVERSAL_STRING => Primitive\UniversalString::class,
        self::TYPE_CHARACTER_STRING => Primitive\CharacterString::class,
        self::TYPE_BMP_STRING => Primitive\BMPString::class
        /* @formatter:on */
    ];
    
    /**
     * Pseudotype for all string types.
     *
     * May be used as an expectation parameter.
     *
     * @var int
     */
    const TYPE_STRING = -1;
    
    /**
     * Pseudotype for all time types.
     *
     * May be used as an expectation parameter.
     *
     * @var int
     */
    const TYPE_TIME = -2;
    
    /**
     * Mapping from universal type tag to human readable name.
     *
     * @internal
     *
     * @var array
     */
    const MAP_TYPE_TO_NAME = [ /* @formatter:off */
        self::TYPE_EOC => "EOC",
        self::TYPE_BOOLEAN => "BOOLEAN",
        self::TYPE_INTEGER => "INTEGER",
        self::TYPE_BIT_STRING => "BIT STRING",
        self::TYPE_OCTET_STRING => "OCTET STRING",
        self::TYPE_NULL => "NULL",
        self::TYPE_OBJECT_IDENTIFIER => "OBJECT IDENTIFIER",
        self::TYPE_OBJECT_DESCRIPTOR => "ObjectDescriptor",
        self::TYPE_EXTERNAL => "EXTERNAL",
        self::TYPE_REAL => "REAL",
        self::TYPE_ENUMERATED => "ENUMERATED",
        self::TYPE_EMBEDDED_PDV => "EMBEDDED PDV",
        self::TYPE_UTF8_STRING => "UTF8String",
        self::TYPE_RELATIVE_OID => "RELATIVE-OID",
        self::TYPE_SEQUENCE => "SEQUENCE",
        self::TYPE_SET => "SET",
        self::TYPE_NUMERIC_STRING => "NumericString",
        self::TYPE_PRINTABLE_STRING => "PrintableString",
        self::TYPE_T61_STRING => "T61String",
        self::TYPE_VIDEOTEX_STRING => "VideotexString",
        self::TYPE_IA5_STRING => "IA5String",
        self::TYPE_UTC_TIME => "UTCTime",
        self::TYPE_GENERALIZED_TIME => "GeneralizedTime",
        self::TYPE_GRAPHIC_STRING => "GraphicString",
        self::TYPE_VISIBLE_STRING => "VisibleString",
        self::TYPE_GENERAL_STRING => "GeneralString",
        self::TYPE_UNIVERSAL_STRING => "UniversalString",
        self::TYPE_CHARACTER_STRING => "CHARACTER STRING",
        self::TYPE_BMP_STRING => "BMPString",
        self::TYPE_STRING => "Any String",
        self::TYPE_TIME => "Any Time"
        /* @formatter:on */
    ];
    
    /**
     * Element's type tag.
     *
     * @var int
     */
    protected $_typeTag;
    
    /**
     *
     * @see \ASN1\Feature\ElementBase::typeClass()
     * @return int
     */
    abstract public function typeClass(): int;
    
    /**
     *
     * @see \ASN1\Feature\ElementBase::isConstructed()
     * @return bool
     */
    abstract public function isConstructed(): bool;
    
    /**
     * Get the content encoded in DER.
     *
     * Returns the DER encoded content without identifier and length header
     * octets.
     *
     * @return string
     */
    abstract protected function _encodedContentDER(): string;
    
    /**
     * Decode type-specific element from DER.
     *
     * @param Identifier $identifier Pre-parsed identifier
     * @param string $data DER data
     * @param int $offset Offset in data to the next byte after identifier
     * @throws DecodeException If decoding fails
     * @return ElementBase
     */
    protected static function _decodeFromDER(Identifier $identifier, string $data,
        int &$offset): ElementBase
    {
        throw new \BadMethodCallException(
            __METHOD__ . " must be implemented in derived class.");
    }
    
    /**
     * Decode element from DER data.
     *
     * @param string $data DER encoded data
     * @param int|null $offset Reference to the variable that contains offset
     *        into the data where to start parsing. Variable is updated to
     *        the offset next to the parsed element. If null, start from offset
     *        0.
     * @throws DecodeException If decoding fails
     * @throws \UnexpectedValueException If called in the context of an expected
     *         type, but decoding yields another type
     * @return ElementBase
     */
    public static function fromDER(string $data, int &$offset = null): ElementBase
    {
        // decode identifier
        $idx = $offset ?? 0;
        $identifier = Identifier::fromDER($data, $idx);
        // determine class that implements type specific decoding
        $cls = self::_determineImplClass($identifier);
        try {
            // decode remaining element
            $element = $cls::_decodeFromDER($identifier, $data, $idx);
        } catch (\LogicException $e) {
            // rethrow as a RuntimeException for unified exception handling
            throw new DecodeException(
                sprintf("Error while decoding %s.",
                    self::tagToName($identifier->intTag())), 0, $e);
        }
        // if called in the context of a concrete class, check
        // that decoded type matches the type of a calling class
        $called_class = get_called_class();
        if (self::class != $called_class) {
            if (!$element instanceof $called_class) {
                throw new \UnexpectedValueException(
                    sprintf("%s expected, got %s.", $called_class,
                        get_class($element)));
            }
        }
        // update offset for the caller
        if (isset($offset)) {
            $offset = $idx;
        }
        return $element;
    }
    
    /**
     *
     * @see \ASN1\Feature\Encodable::toDER()
     * @return string
     */
    public function toDER(): string
    {
        $identifier = new Identifier($this->typeClass(),
            $this->isConstructed() ? Identifier::CONSTRUCTED : Identifier::PRIMITIVE,
            $this->_typeTag);
        $content = $this->_encodedContentDER();
        $length = new Length(strlen($content));
        return $identifier->toDER() . $length->toDER() . $content;
    }
    
    /**
     *
     * @see \ASN1\Feature\ElementBase::tag()
     * @return int
     */
    public function tag(): int
    {
        return $this->_typeTag;
    }
    
    /**
     *
     * @see \ASN1\Feature\ElementBase::isType()
     * @return bool
     */
    public function isType(int $tag): bool
    {
        // if element is context specific
        if ($this->typeClass() == Identifier::CLASS_CONTEXT_SPECIFIC) {
            return false;
        }
        // negative tags identify an abstract pseudotype
        if ($tag < 0) {
            return $this->_isPseudoType($tag);
        }
        return $this->_isConcreteType($tag);
    }
    
    /**
     *
     * @see \ASN1\Feature\ElementBase::expectType()
     * @return ElementBase
     */
    public function expectType(int $tag): ElementBase
    {
        if (!$this->isType($tag)) {
            throw new \UnexpectedValueException(
                sprintf("%s expected, got %s.", self::tagToName($tag),
                    $this->_typeDescriptorString()));
        }
        return $this;
    }
    
    /**
     * Check whether the element is a concrete type of a given tag.
     *
     * @param int $tag
     * @return bool
     */
    private function _isConcreteType(int $tag): bool
    {
        // if tag doesn't match
        if ($this->tag() != $tag) {
            return false;
        }
        // if type is universal check that instance is of a correct class
        if ($this->typeClass() == Identifier::CLASS_UNIVERSAL) {
            $cls = self::_determineUniversalImplClass($tag);
            if (!$this instanceof $cls) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Check whether the element is a pseudotype.
     *
     * @param int $tag
     * @return bool
     */
    private function _isPseudoType(int $tag): bool
    {
        switch ($tag) {
            case self::TYPE_STRING:
                return $this instanceof StringType;
            case self::TYPE_TIME:
                return $this instanceof TimeType;
        }
        return false;
    }
    
    /**
     *
     * @see \ASN1\Feature\ElementBase::isTagged()
     * @return bool
     */
    public function isTagged(): bool
    {
        return $this instanceof TaggedType;
    }
    
    /**
     *
     * @see \ASN1\Feature\ElementBase::expectTagged()
     * @return TaggedType
     */
    public function expectTagged($tag = null): TaggedType
    {
        if (!$this->isTagged()) {
            throw new \UnexpectedValueException(
                sprintf("Context specific element expected, got %s.",
                    Identifier::classToName($this->typeClass())));
        }
        if (isset($tag) && $this->tag() != $tag) {
            throw new \UnexpectedValueException(
                sprintf("Tag %d expected, got %d.", $tag, $this->tag()));
        }
        return $this;
    }
    
    /**
     *
     * @see \ASN1\Feature\ElementBase::asElement()
     * @return Element
     */
    final public function asElement(): Element
    {
        return $this;
    }
    
    /**
     * Get element decorated with UnspecifiedType object.
     *
     * @return UnspecifiedType
     */
    public function asUnspecified(): UnspecifiedType
    {
        return new UnspecifiedType($this);
    }
    
    /**
     * Determine the class that implements the type.
     *
     * @param Identifier $identifier
     * @return string Class name
     */
    protected static function _determineImplClass(Identifier $identifier): string
    {
        switch ($identifier->typeClass()) {
            case Identifier::CLASS_UNIVERSAL:
                return self::_determineUniversalImplClass($identifier->intTag());
            case Identifier::CLASS_CONTEXT_SPECIFIC:
                return TaggedType::class;
            case Identifier::CLASS_APPLICATION:
                return ApplicationType::class;
            case Identifier::CLASS_PRIVATE:
                return PrivateType::class;
        }
        throw new \UnexpectedValueException(
            sprintf("%s %d not implemented.",
                Identifier::classToName($identifier->typeClass()),
                $identifier->tag()));
    }
    
    /**
     * Determine the class that implements an universal type of the given tag.
     *
     * @param int $tag
     * @throws \UnexpectedValueException
     * @return string Class name
     */
    protected static function _determineUniversalImplClass(int $tag): string
    {
        if (!array_key_exists($tag, self::MAP_TAG_TO_CLASS)) {
            throw new \UnexpectedValueException(
                "Universal tag $tag not implemented.");
        }
        return self::MAP_TAG_TO_CLASS[$tag];
    }
    
    /**
     * Get textual description of the type for debugging purposes.
     *
     * @return string
     */
    protected function _typeDescriptorString(): string
    {
        if ($this->typeClass() == Identifier::CLASS_UNIVERSAL) {
            return self::tagToName($this->_typeTag);
        }
        return sprintf("%s TAG %d", Identifier::classToName($this->typeClass()),
            $this->_typeTag);
    }
    
    /**
     * Get human readable name for an universal tag.
     *
     * @param int $tag
     * @return string
     */
    public static function tagToName(int $tag): string
    {
        if (!array_key_exists($tag, self::MAP_TYPE_TO_NAME)) {
            return "TAG $tag";
        }
        return self::MAP_TYPE_TO_NAME[$tag];
    }
}
