<?php
//
// Definition of eZImage class
//
// Created on: <30-Apr-2002 16:47:08 bf>
//
// Copyright (C) 1999-2003 eZ systems as. All rights reserved.
//
// This source file is part of the eZ publish (tm) Open Source Content
// Management System.
//
// This file may be distributed and/or modified under the terms of the
// "GNU General Public License" version 2 as published by the Free
// Software Foundation and appearing in the file LICENSE.GPL included in
// the packaging of this file.
//
// Licencees holding valid "eZ publish professional licences" may use this
// file in accordance with the "eZ publish professional licence" Agreement
// provided with the Software.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The "eZ publish professional licence" is available at
// http://ez.no/products/licences/professional/. For pricing of this licence
// please contact us via e-mail to licence@ez.no. Further contact
// information is available at http://ez.no/home/contact/.
//
// The "GNU General Public License" (GPL) is available at
// http://www.gnu.org/copyleft/gpl.html.
//
// Contact licence@ez.no if any conditions of this licencing isn't clear to
// you.
//

/*!
  \class eZImage ezimage.php
  \ingroup eZKernel
  \brief The class eZImage handles registered images

*/

include_once( "lib/ezdb/classes/ezdb.php" );
include_once( "kernel/classes/ezpersistentobject.php" );
include_once( "kernel/classes/ezcontentclassattribute.php" );
include_once( "kernel/classes/datatypes/ezimage/ezimagevariation.php");

class eZImage extends eZPersistentObject
{
    function eZImage( $row )
    {
        $this->eZPersistentObject( $row );
    }

    function &definition()
    {
        return array( "fields" => array( "contentobject_attribute_id" => "ContentObjectAttributeID",
                                         "version" => "Version",
                                         "filename" => "Filename",
                                         "original_filename" => "OriginalFilename",
                                         "mime_type" => "MimeType",
                                         "alternative_text" => "AlternativeText"
                                         ),
                      "keys" => array( "contentobject_attribute_id", "version" ),
                      "relations" => array( "contentobject_attribute_id" => array( "class" => "ezcontentobjectattribute",
                                                                                   "field" => "id" ) ),
                      "class_name" => "eZImage",
                      "name" => "ezimage" );
    }

    function attributes()
    {
        return eZPersistentObject::attributes();
    }

    function hasAttribute( $attr )
    {
        return $attr == "mime_type_category" or $attr == "mime_type_part" or eZPersistentObject::hasAttribute( $attr ) or
            $attr == "small" or $attr == "large" or $attr == "medium" or $attr == "reference" or $attr == "original";
    }

    function &attribute( $attr )
    {
        $ini =& eZINI::instance();

        switch( $attr )
        {
            case "mime_type_category":
            {
                $types = explode( "/", eZPersistentObject::attribute( "mime_type" ) );
                return $types[0];
            }break;
            case "mime_type_part":
            {
                $types = explode( "/", eZPersistentObject::attribute( "mime_type" ) );
                return $types[1];
            } break;
            case "small":
            case "medium":
            case "large":
            case "reference":
            case "original":
            {
                if ( $attr == "small" )
                {
                    $width = $ini->variable( "ImageSettings" , "SmallSizeWidth" );
                    $height = $ini->variable( "ImageSettings" , "SmallSizeHeight" );
                }
                else if ( $attr == "medium" )
                {
                    $width = $ini->variable( "ImageSettings" , "MediumSizeWidth" );
                    $height = $ini->variable( "ImageSettings" , "MediumSizeHeight" );
                }
                else if ( $attr == "large" )
                {
                    $width = $ini->variable( "ImageSettings" , "LargeSizeWidth" );
                    $height = $ini->variable( "ImageSettings" , "LargeSizeHeight" );
                }
                else if ( $attr == "reference" )
                {
                    $width = $ini->variable( "ImageSettings" , "ReferenceSizeWidth" );
                    $height = $ini->variable( "ImageSettings" , "ReferenceSizeHeight" );
                }
                else
                {
                    $width = false;
                    $height = false;
                }

                $cacheString = $this->ContentObjectAttributeID . '-' . $attr . "-" . $width . "-" . $height;
                if ( $attr == "original" )
                    $cacheString = $this->ContentObjectAttributeID . '-' . $attr;

                if ( !isset( $GLOBALS[$cacheString] ) )
                {
                    if ( $attr == "original" )
                        $img_variation =& eZImageVariation::createOriginal( $this->ContentObjectAttributeID, $this->Version, $this->Filename, eZDir::getPathFromFilename( $this->Filename ) );
                    else
                        $img_variation =& eZImageVariation::requestVariation( $this, $width, $height );
                    $GLOBALS[$cacheString] =& $img_variation;
                }
                else
                {
                    $img_variation =& $GLOBALS[$cacheString];
                }
                return $img_variation;
            }break;
            default:
                return eZPersistentObject::attribute( $attr );
        }
        return null;
    }

    function &create( $contentObjectAttributeID, $contentObjectAttributeVersion  )
    {
        $row = array( "contentobject_attribute_id" => $contentObjectAttributeID,
                      "version" => $contentObjectAttributeVersion,
                      "filename" => "",
                      "original_filename" => "",
                      "mime_type" => ""
                      );
        return new eZImage( $row );
    }

    function &fetch( $id, $version = null, $asObject = true )
    {
        if( $version == null )
        {
            return eZPersistentObject::fetchObjectList( eZImage::definition(),
                                                        null,
                                                        array( "contentobject_attribute_id" => $id ),
                                                        null,
                                                        null,
                                                        $asObject );
        }
        else
        {
            return eZPersistentObject::fetchObject( eZImage::definition(),
                                                    null,
                                                    array( "contentobject_attribute_id" => $id,
                                                           "version" => $version ),
                                                    $asObject );
        }
    }

    function &fetchList( $asObject = true )
    {
        return eZPersistentObject::fetchObjectList( eZImage::definition(),
                                                    null, null, null, null,
                                                    $asObject );
    }

    function &remove( $id, $version )
    {
        if( $version == null )
        {
            eZPersistentObject::removeObject( eZImage::definition(),
                                              array( "contentobject_attribute_id" => $id ) );
        }
        else
        {
            eZPersistentObject::removeObject( eZImage::definition(),
                                              array( "contentobject_attribute_id" => $id,
                                                     "version" => $version ) );
        }
    }

    var $Version;
    var $ContentObjectAttributeID;
    var $Filename;
    var $OriginalFilename;
    var $MimeType;
}

?>
