
<h1>Site languages</h1>

<div class="tableheader">
</div>


<table id="view">
    <tr>
        <th class="actions">Actions</th>
        <th>Name</th>
        <th>Code</th>
        <th>National Name</th>
    </tr>
<?php foreach ( $langs as $lang ): ?>
    <tr>
        <td><?php echo $this->Controls->edit($lang['Language']['id']) ?></td>    
        <td><?php echo $lang['Language']['name'] ?></td>
        <td><?php echo $lang['Language']['code'] ?></td>
        <td><?php echo $lang['Language']['nationalname'] ?></td>
    </tr>
<?php endforeach ?>
</table>
