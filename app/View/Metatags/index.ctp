<?php $this->Paginator->options($options) ?>

<table class="datatable">
    <caption>Meta-tags Management</caption>
    <tr>
        <td class="filtercontainer" colspan="9">
        <form method="post" id="filterform" action="">
        <div class="filter">
            <fieldset>
                <legend>Filter</legend>
                    <div class="data">
                      <div class="row">
                         <?php echo $filter->input('Metatag', 'url', 'URL:') ?>
                         <?php echo $filter->input('Metatag', 'name', 'Name:') ?>
                         <?php echo $filter->input('Metatag', 'action', 'Action:') ?>
                         <?php echo $filter->language('Metatag', 'Language:') ?>
                      </div>
                      <div class="row">
                         <?php echo $filter->input('Metatag', 'title', 'title:') ?>
                         <?php echo $filter->input('Metatag', 'keywords', 'Keywords:') ?>
                         <?php echo $filter->input('Metatag', 'author', 'Author:') ?>
                         <?php echo $filter->input('Metatag', 'description', 'Description:') ?>
                      </div>
                    </div>
                      <?php echo $this->Form->button('Apply', array('type' => 'submit')) ?>
                      <?php echo $this->Form->button('Reset', array('type' => 'reset')) ?>
            </fieldset>
            </div>
            </form>
        </td>
    </tr>
    <tr>
        <th><?php echo $this->Paginator->sort('Url', 'url') ?></th>
        <th><?php echo $this->Paginator->sort('Name', 'name') ?></th>
        <th><?php echo $this->Paginator->sort('Action', 'action') ?></th>
        <th><?php echo $this->Paginator->sort('Lang', 'code') ?></th>
        <th><?php echo $this->Paginator->sort('Title', 'title') ?></th>
        <th><?php echo $this->Paginator->sort('Keywords', 'keywords') ?></th>
        <th><?php echo $this->Paginator->sort('Author', 'author') ?></th>
        <th><?php echo $this->Paginator->sort('Description', 'description') ?></th>
        <th><?php echo $this->Controls->search() ?></th>
    </tr>
    <?php foreach ( $metatags as $tag ): ?>
      <tr class="data">
          <td><?php echo $tag['Metatag']['url']         ?></td>
          <td><?php echo $tag['Metatag']['name']        ?></td>
          <td><?php echo $tag['Metatag']['action']      ?></td>
          <td><?php echo $tag['Language']['code']       ?></td>
          <td><?php echo $tag['Metatag']['title']       ?></td>
          <td><?php echo $tag['Metatag']['keywords']    ?></td>
          <td><?php echo $tag['Metatag']['author']      ?></td>
          <td><?php echo $tag['Metatag']['description'] ?></td>
          <td>
        <?php echo $this->Controls->edit($tag['Metatag']['id']) ?>
            <?php if ( DEV_MODE ): ?>
              <?php echo $this->Controls->delete($tag['Metatag']['id']) ?>
            <?php endif; ?>
          </td>
      </tr>
   <?php endforeach; ?>
    <tr>
        <td class="header" colspan="9">
            <?php echo $this->element('paginator') ?>
        </td>
    </tr>
    <tr>
        <td class="footer" colspan="9">
            <?php echo $this->element('pagingnav') ?>
        </td>
    </tr>
</table>
