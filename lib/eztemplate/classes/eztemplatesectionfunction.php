<?php
//
// Definition of eZTemplateSectionFunction class
//
// Created on: <01-Mar-2002 13:50:33 amos>
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
  \class eZTemplateSectionFunction eztemplatesectionfunction.php
  \ingroup eZTemplateFunctions
  \brief Advanced block handling in templates using function "section".

  This class can be used in several different ways. It's primary
  use is for display an array of elements using the loop parameter.
  The array is iterated and the text of all the children are appended
  for each element. The current key and item is set in the namespace
  provided by the parameter name. If the paremeter sequence is supplied
  and it is an array it will be iterated and each element will be set
  in the variable sequence, when the end of the sequence is reached it
  is restarted.

\code
// Example of template code
{* Loop 5 times *}
{section loop=5}
{$item}
{/section}
\endcode


Add these:
{section name=adsfsdf sequence=array(234,234,23,4,234) loop=.. max=2 offset=0 exclude}
{section-exclude check=id using=array(1,2,5)}
{section-exclude check=array(class,id) using=array(1,2,5)}
{section-include check=class_id using=array(1,2,5)}

asdfasdf


{delimiter}asdfasdf{/delimiter}
{/section}

{$module.features.list(5,array(2,5))}


{section loop=... max=10 offset=2}
{/section}



*/


class eZTemplateSectionFunction
{
    /*!
     Initializes the object with a name, the name is required for determining
     the name of the -else tag.
    */
    function eZTemplateSectionFunction( $name = "section" )
    {
        $this->Name = $name;
    }

    /*!
     Returns the attribute list which is delimiter and $name-else,
     where $name is the name of the function.
    */
    function attributeList()
    {
        return array( "delimiter" => true,
                      "section-exclude" => false,
                      "section-include" => false,
                      $this->Name . "-else" => false );
    }

    /*!
     Returns an array containing the name of the section function, default is "section".
     The name is specified in the constructor.
    */
    function functionList()
    {
        return array( $this->Name );
    }
    /*!
     Processes the function with all it's children.
    */
    function process( &$tpl, &$textElements, $functionName, $functionChildren, $functionParameters, $functionPlacement, $rootNamespace, $currentNamespace )
    {
//         $text = "";
        $children = $functionChildren;
        $parameters = $functionParameters;
        $name = null;
        if ( isset( $parameters["name"] ) )
             $name = $tpl->elementValue( $parameters["name"], $rootNamespace, $currentNamespace, $functionPlacement );
        if ( $name === null )
        {
            $name = $currentNamespace;
        }
        else
        {
            if ( $currentNamespace != "" )
                $name = "$currentNamespace:$name";
        }
        $loopItem = null;
        $hasLoopItemParameter = false;
        if ( isset( $parameters["loop"] ) )
        {
            $hasLoopItemParameter = true;
            $loopItem =& $tpl->elementValue( $parameters["loop"], $rootNamespace, $currentNamespace, $functionPlacement );
        }
//         eZDebug::writeNotice( $hasLoopItemParameter, "\$hasLoopItemParameter" );
//         eZDebug::writeNotice( $loopItem, "\$loopItem" );
        /// \todo Check if this needs removing
        if ( $hasLoopItemParameter and $loopItem === null )
            return;
        $showItem = null;
        if ( isset( $parameters["show"] ) )
            $showItem =& $tpl->elementValue( $parameters["show"], $rootNamespace, $currentNamespace, $functionPlacement );
        $sequenceStructure = null;
        if ( isset( $parameters["sequence"] ) )
            $sequenceStructure = $tpl->elementValue( $parameters["sequence"], $rootNamespace, $currentNamespace, $functionPlacement );
        $iterationMaxCount = false;
        if ( isset( $parameters["max"] ) )
        {
            $iterationMaxCount =& $tpl->elementValue( $parameters["max"], $rootNamespace, $currentNamespace, $functionPlacement );
            if ( is_array( $iterationMaxCount ) )
            {
                $iterationMaxCount = count( $iterationMaxCount );
            }
            else if ( !is_numeric( $iterationMaxCount ) )
            {
                $tpl->warning( $functionName, "Wrong parameter type for 'max', use either numerical or arrays" );
            }
        }
        $iterationOffset = false;
        if ( isset( $parameters["offset"] ) )
        {
            $iterationOffset =& $tpl->elementValue( $parameters["offset"], $rootNamespace, $currentNamespace, $functionPlacement );
            if ( is_array( $iterationOffset ) )
            {
                $iterationOffset = count( $iterationOffset );
            }
            else if ( !is_numeric( $iterationOffset ) )
            {
                $tpl->warning( $functionName, "Wrong parameter type for 'offset', use either numerical or arrays" );
            }
        }

//         eZDebug::writeNotice( $showItem, "\$showItem" );
//         eZDebug::writeNotice( $sequenceStructure, "\$sequenceStructure" );

        $elseName = $functionName . "-else";
        $delimiterStructure = null;
        $filterStructure = array();
        $else = null;
        $shown = 1;
        $items = array();
        $items[0] = array();
        $items[1] = array();
//         eZDebug::writeDebug( $children, 'children' );
        foreach ( array_keys( $children ) as $childKey )
        {
            $child =& $children[$childKey];
            $childType = $child[0];
            if ( $childType == EZ_TEMPLATE_NODE_FUNCTION )
            {
                switch ( $child[2] )
                {
                    case "delimiter":
                    {
                        if ( $shown === 1 and $delimiterStructure === null )
                        {
                            $delimiterStructure =& $child;
                        }
                    } break;
                    case "section-exclude":
                    case "section-include":
                    {
                        if ( $shown === 1 )
                            $filterStructure[] =& $child;
                    } break;
                    case $elseName:
                    {
                        $else =& $child;
                        $shown = 0;
                    } break;
                    default:
                    {
                        $items[$shown][] =& $child;
                    } break;
                }
            }
            else
            {
                $items[$shown][] =& $child;
            }
        }
//         eZDebug::writeNotice( $filterStructure, "\$filterStructure" );

//         if ( !$showItem )
//             return $text;
//         eZDebug::writeNotice( "got here" );
        $canShowBlock = true;
        if( $showItem !== null and ( ( is_array( $showItem ) and count( $showItem ) == 0 ) or
                                     ( is_numeric( $showItem ) and $showItem == 0 ) or
                                     ( is_string( $showItem ) > 0 and strlen( $showItem ) == 0 ) or
                                     !$showItem ) )
            $canShowBlock = false;


        if ( ( $showItem === null or ( $showItem !== null and $canShowBlock ) ) and $loopItem === null )
        {
//             eZDebug::writeNotice( "Running default looping" );
            $this->processChildrenOnce( $textElements, $items[1], $tpl, $rootNamespace, $name );
        }
        else
        {
//             eZDebug::writeNotice( "Running non-default looping" );
            $showMainBody = true;
            if ( $showItem !== null )
            {
                if( !$canShowBlock )
                    $showMainBody = false;
            }
//             else if ( ( is_array( $loopItem ) and count( $loopItem ) == 0 ) or
//                       ( is_numeric( $loopItem ) and $loopItem == 0 ) or
//                       ( is_string( $loopItem ) > 0 and strlen( $loopItem ) == 0 ) )
//                 $showMainBody = false;
            if ( $showMainBody )
            {
                $isFirstRun = true;
                $index = 0;
                if ( is_array( $loopItem ) )
                {
//                     eZDebug::writeDebug( "Running array looping" );
                    $array =& $loopItem;
                    $arrayKeys =& array_keys( $array );
                    if ( $iterationOffset !== false )
                        $arrayKeys = array_splice( $arrayKeys, $iterationOffset );
                    if ( $iterationMaxCount !== false )
                        $arrayKeys = array_splice( $arrayKeys, 0, $iterationMaxCount );
                    foreach ( $arrayKeys as $key )
                    {
                        $item =& $array[$key];
                        $this->processChildren( $textElements, $items[1], $key, $item, $index, $isFirstRun,
                                                $delimiterStructure, $sequenceStructure, $filterStructure,
                                                $tpl, $rootNamespace, $name, $functionPlacement );
                    }
                }
                else if ( is_numeric( $loopItem ) )
                {
//                     eZDebug::writeNotice( "Running numeric looping" );
                    $value = $loopItem;
                    $count = $value;
                    if ( $value < 0 )
                        $count = -$count;
                    if ( $iterationMaxCount !== false )
                        $count = min( $count, $iterationMaxCount );
                    $loopStart = 0;
                    if ( $iterationOffset !== false )
                        $loopStart = $iterationOffset;
                    for ( $i = $loopStart; $i < $count; ++$i )
                    {
                        if ( $value < 0 )
                        {
                            $key = -$i;
                            $item = -$i - 1;
                        }
                        else
                        {
                            $key = $i;
                            $item = $i + 1;
                        }
                        $this->processChildren( $textElements, $items[1], $key, $item, $index, $isFirstRun,
                                                $delimiterStructure, $sequenceStructure, $filterStructure,
                                                $tpl, $rootNamespace, $name, $functionPlacement );
                    }
                }
                else if ( is_string( $loopItem ) )
                {
//                     eZDebug::writeNotice( "Running string looping" );
                    $text =& $loopItem;
                    $stringLength = strlen( $text );
                    if ( $iterationMaxCount !== false )
                        $stringLength = min( $stringLength, $iterationMaxCount );
                    $loopStart = 0;
                    if ( $iterationOffset !== false )
                        $loopStart = $iterationOffset;
                    for ( $i = $loopStart; $i < $stringLength; ++$i )
                    {
                        $key = $i;
                        $item = $text[$i];
                        $this->processChildren( $textElements, $items[1], $key, $item, $index, $isFirstRun,
                                                $delimiterStructure, $sequenceStructure, $filterStructure,
                                                $tpl, $rootNamespace, $name, $functionPlacement );
                    }
                }
                if ( !$isFirstRun )
                {
                    $tpl->unsetVariable( "key", $name );
                    $tpl->unsetVariable( "item", $name );
                    $tpl->unsetVariable( "index", $name );
                    $tpl->unsetVariable( "number", $name );
                    if ( $sequenceStructure !== null and is_array( $sequenceStructure ) )
                        $tpl->unsetVariable( "sequence", $name );
                }
            }
            else
            {
//                 eZDebug::writeNotice( "no loop items" );
                $this->processChildrenOnce( $textElements, $items[0], $tpl, $rootNamespace, $name );
            }
        }
    }

    function processChildrenOnce( &$textElements, &$children, &$tpl, $rootNamespace, $name )
    {
        foreach ( array_keys( $children ) as $childKey )
        {
            $child =& $children[$childKey];
            $tpl->processNode( $child, $textElements, $rootNamespace, $name );
        }
    }

    function processChildren( &$textElements,
                              &$children, $key, &$item, &$index, &$isFirstRun,
                              &$delimiterStructure, &$sequenceStructure, &$filterStructure,
                              &$tpl, $rootNamespace, $name, $functionPlacement )
    {
        $tpl->setVariable( "key", $key, $name );
        $tpl->setVariable( "item", $item, $name );
        $tpl->setVariable( "index", $index, $name );
        $tpl->setVariable( "number", $index + 1, $name );
        if ( count( $filterStructure ) > 0 )
        {
            $filterCount = count( $filterStructure );
            $includeElement = true;
            for ( $i = 0; $i < $filterCount; ++$i )
            {
                $filterElement =& $filterStructure[$i];
                $filterParameters =& $filterElement[3];
                $filterName = $filterElement[2];
                $filterMatch = null;
                if ( isset( $filterParameters["match"] ) )
                {
                    $filterMatch = $tpl->elementValue( $filterParameters["match"], $rootNamespace, $name, $functionPlacement );
                    if ( $filterMatch )
                        $includeElement = $filterName == "section-exclude" ? false : true;
                }
                else
                    $tpl->missingParameter( "section:$filterName", "match" );
            }
            if ( !$includeElement )
                return;
        }
        if ( $delimiterStructure !== null and !$isFirstRun )
        {
            $delimiterParameters = $delimiterStructure[3];
            $delimiterMatch = true;
            if ( isset( $delimiterParameters["modulo"] ) )
            {
                $delimiterModulo = $delimiterParameters["modulo"];
                $modulo = $tpl->elementValue( $delimiterModulo, $rootNamespace, $name, $functionPlacement );
                if ( is_numeric( $modulo ) )
                    $delimiterMatch = ( $index % $modulo ) == 0;
            }
            if ( isset( $delimiterParameters["match"] ) )
            {
                $delimiterMatchParameter = $delimiterParameters["match"];
                $delimiterMatch = $tpl->elementValue( $delimiterMatchParameter, $rootNamespace, $name, $functionPlacement );
            }
            if ( $delimiterMatch )
            {
                $delimiterChildren =& $delimiterStructure[1];
                foreach ( array_keys( $delimiterChildren ) as $delimiterChildKey )
                {
                    $delimiterChild =& $delimiterChildren[$delimiterChildKey];
                    $tpl->processNode( $delimiterChild, $textElements, $rootNamespace, $name );
                }
            }
        }
        $isFirstRun = false;
        if ( $sequenceStructure !== null and is_array( $sequenceStructure ) )
        {
            $sequenceValue = array_shift( $sequenceStructure );
            $tpl->setVariable( "sequence", $sequenceValue, $name );
            $sequenceStructure[] = $sequenceValue;
        }
        foreach ( array_keys( $children ) as $childKey )
        {
            $child =& $children[$childKey];
            $tpl->processNode( $child, $textElements, $rootNamespace, $name );
        }
        ++$index;
    }

    /*!
     Returns true.
    */
    function hasChildren()
    {
        return true;
    }

    /// \privatesection
    /// Name of the function
    var $Name;
}

?>
