<div class="events index">
<h2>Team Ratings</h2>
<table cellpadding="0" cellspacing="0">
<tr >
    <th>Rating Difference</th>
    <th>-5</th>
    <th>-4</th>
    <th>-3</th>
    <th>-2</th>
    <th>-1</th>
    <th>0</th>
    <th>1</th>
    <th>2</th>
    <th>3</th>
    <th>4</th>
    <th>5</th>
</tr>
<?php for ($diffCtr = 0; $diffCtr <= 30; $diffCtr++):?>
    <tr>
        <td><?php echo $diffCtr * 50; ?> </td>
        <?php for ($cdCtr = -5; $cdCtr <=5; $cdCtr++):      ?>
            <td><?php printf("%1.1F",$results[$diffCtr][$cdCtr]); ?></td>
        <?php endfor; ?>
    </tr>
<?php endfor; ?>
</table>