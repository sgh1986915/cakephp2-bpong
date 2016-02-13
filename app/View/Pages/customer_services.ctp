<h2>Customer Service Panel</h2>
<?php if ($show_all_orders):?>
<a href="/StoreOrders/cs_index"> Orders </a><br/>
<?php endif;?>
<?php if ($show_ipg_orders):?>
<a href="/storeOrders/warehouse_index/3">IPG Warehouse-specific Orders</a><br/>
<?php endif;?>
<?php if ($show_sae_orders):?>
<a href="/storeOrders/warehouse_index/4">Sign Art Warehouse-specific Orders</a><br/>
<?php endif;?>