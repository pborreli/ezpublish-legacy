{let page_limit=20
     reply_limit=cond( $view_parameters.offset|gt( 0 ), 20,
                       19 )
     reply_offset=cond( $view_parameters.offset|gt( 0 ), sub( $view_parameters.offset, 1 ),
                        $view_parameters.offset )
     reply_list=fetch('content','list', hash( parent_node_id, $node.node_id,
                                              limit, $reply_limit,
                                              offset, $reply_offset,
                                              sort_by, array( array( published, true() ) ) ) )
     reply_count=fetch('content','list_count', hash( parent_node_id, $node.node_id ) )
     previous_topic=fetch_alias( subtree, hash( parent_node_id, $node.parent_node_id,
                                                class_filter_type, include,
                                                class_filter_array, array( 'forum_topic' ),
                                                limit, 1,
                                                attribute_filter, array( and, array( 'published', '<', $node.object.published ) ),
                                                sort_by, array( 'published', false() ) ) )
     next_topic=fetch_alias( subtree, hash( parent_node_id, $node.parent_node_id,
                                            class_filter_type, include,
                                            class_filter_array, array( 'forum_topic' ),
                                            limit, 1,
                                            attribute_filter, array( and, array( 'published', '>', $node.object.published ) ),
                                            sort_by, array( 'published', true() ) ) ) }


<div class="content-view-full">
    <div class="class-forum">

        <h1>{$node.name|wash}</h1>

        <div class="content-navigator">
            {section show=$previous_topic}
                <div class="content-navigator-previous">
                    <div class="content-navigator-arrow">&laquo;&nbsp;</div><a href={$previous_topic[0].url_alias|ezurl} title="{$previous_topic[0].name|wash}">{'Previous topic'|i18n( 'design/base' )}</a>
                </div>
            {section-else}
                <div class="content-navigator-previous-disabled">
                    <div class="content-navigator-arrow">&laquo;&nbsp;</div>{'Previous topic'|i18n( 'design/base' )}
                </div>
            {/section}

            {section show=and( $previous_topic, $next_topic )}
                <div class="content-navigator-separator">|</div>
            {section-else}
                <div class="content-navigator-separator-disabled">|</div>
            {/section}

            {section show=$next_topic}
                <div class="content-navigator-next">
                    <a href={$next_topic[0].url_alias|ezurl} title="{$next_topic[0].name|wash}">{'Next topic'|i18n( 'design/base' )}</a><div class="content-navigator-arrow">&nbsp;&raquo;</div>
                </div>
            {section-else}
                <div class="content-navigator-next-disabled">
                    {'Next topic'|i18n( 'design/base' )}<div class="content-navigator-arrow">&nbsp;&raquo;</div>
                </div>
            {/section}
        </div>

        {section show=$node.object.can_create}
        <form method="post" action={"content/action/"|ezurl}>
            <input class="button" type="submit" name="NewButton" value="New reply" />
            <input type="hidden" name="ContentNodeID" value="{$node.node_id}" />
            <input type="hidden" name="ContentObjectID" value="{$node.contentobject_id}" />
            <input class="button" type="submit" name="ActionAddToNotification" value="Keep me updated" />
            <input type="hidden" name="NodeID" value="{$node.node_id}" />
            <input type="hidden" name="ClassIdentifier" value="forum_reply" />
        </form>
        {section-else}
           <p>
            {"You need to be logged in to get access to the forums. You can do so"|i18n("design/base")} <a href={"/user/login/"|ezurl}>{"here"|i18n("design/base")}</a>
           </p>
        {/section}

        <div class="content-view-children">
            <table class="list forum" cellspacing="0">
            <tr>
                <th class="author">
                    {"Author"|i18n("design/base")}
                </th>
                <th class="message">
                    {"Message"|i18n("design/base")}
                 </th>
            </tr>
            {section show=$view_parameters.offset|lt( 1 )}
            <tr>
               <td class="author">
               {let owner=$node.object.owner owner_map=$owner.data_map}
                   <p class="author">{$owner.name|wash}
                   {section show=is_set( $owner_map.title )}
                       <br/>{$owner_map.title.content|wash}
                   {/section}</p>
                   {section show=$owner_map.image.has_content}
                   <div class="authorimage">
                      {attribute_view_gui attribute=$owner_map.image image_class=small}
                   </div>
                   {/section}

                   {section show=is_set( $owner_map.location )}
                       <p>{"Location"|i18n( "design/base" )}: {$owner_map.location.content|wash}</p>
                   {/section}
                   <p>
                      {let owner_id=$node.object.owner_id}
                          {section var=author loop=$node.object.author_array}
                              {section show=eq($owner_id,$author.contentobject_id)|not()}
                                  {"Moderated by"|i18n( "design/base" )}: {$author.contentobject.name}
                              {/section}
                          {/section}
                      {/let}
                   </p>

                  {section show=$node.object.can_edit}
                      <form method="post" action={"content/action/"|ezurl}>
                          <input type="hidden" name="ContentObjectID" value="{$node.object.id}" />
                          <input class="button" type="submit" name="EditButton" value="{'Edit'|i18n('design/standard/node/view')}" />
                      </form>
                  {/section}
               </td>
               <td class="message">
                   <p class="date">{$node.object.published|l10n(datetime)}</p>
                   <h2>{$node.name|wash}</h2>
                   <p>
                       {$node.object.data_map.message.content|simpletags|wordtoimage|autolink}
                   </p>
                   {section show=$owner_map.signature.has_content}
                       <p class="author-signature">{$owner_map.signature.content|simpletags|autolink}</p>
                   {/section}
               </td>
               {/let}
           </tr>
           {/section}

           {section var=reply loop=$reply_list sequence=array( bgdark, bglight )}
           <tr class="{$reply.sequence}">
               <td class="author">
               {let owner=$reply.object.owner owner_map=$owner.data_map}
                   <p class="author">{$owner.name|wash}
                   {section show=is_set( $owner_map.title )}
                       <br/>{$owner_map.title.content|wash}
                   {/section}</p>

                   {section show=$owner_map.image.has_content}
                   <div class="authorimage">
                      {attribute_view_gui attribute=$owner_map.image image_class=small}
                   </div>
                   {/section}

                   {section show=is_set( $owner_map.location )}
                       <p>{"Location"|i18n( "design/base" )}: {$owner_map.location.content|wash}</p>
                   {/section}

                   {let owner_id=$reply.object.owner.id}
                       {section var=author loop=$reply.object.author_array}
                           {section show=ne( $reply.object.owner_id, $author.contentobject_id )}
                               <p>
                                   {'Moderated by'|i18n( 'design/base' )}: {$author.contentobject.name}
                               </p>
                           {/section}
                       {/section}
                   {/let}

                   {switch match=$reply.object.can_edit}
                   {case match=1}
                       <form method="post" action={"content/action/"|ezurl}>
                       <input type="hidden" name="ContentObjectID" value="{$reply.object.id}" />
                       <input class="button" type="submit" name="EditButton" value="{'Edit'|i18n('design/standard/node/view')}" />
                       </form>
                   {/case}
                   {case match=0}
                   {/case}
                   {/switch}

               </td>
               <td class="message">
                   <p class="date">{$reply.object.published|l10n( datetime )}</p>

                   <h2 id="msg{$reply.node_id}">{$reply.name|wash}</h2>
                   <p>
                       {$reply.object.data_map.message.content|simpletags|wordtoimage|autolink}
                   </p>

                   {section show=$owner_map.signature.has_content}
                       <p class="author-signature">{$owner_map.signature.content|simpletags|autolink}</p>
                   {/section}
               {/let}
               </td>
           </tr>
           {/section}

           </table>

        </div>

    </div>
</div>

{include name=navigator
         uri='design:navigator/google.tpl'
         page_uri=$node.url_alias
         item_count=$reply_count
         view_parameters=$view_parameters
         item_limit=$page_limit}


