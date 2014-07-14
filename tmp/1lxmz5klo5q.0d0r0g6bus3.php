<div id="rightarea-content">
    <table id="register_table">
        <th id="heading" class="table_title">Registered Donors</th>
		<?php foreach (($donorlist?:array()) as $groupname): ?>
                <tr>
                    <td>
                        <?php echo $groupname['group']; ?>
                    </td>
                    <td>
                        <?php echo $groupname['count']; ?>
                    </td>
                </tr>
		<?php endforeach; ?>
    </table>
</div>