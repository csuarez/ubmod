<div style="padding:10px;">
  <?php if ($user): ?>
    <script type="text/javascript">
        Ext.onReady(function () {
            var params = <?php echo $params ?>;

            Ubmod.app.extendPanelHeight();

            <?php if ($interval['multi_month']): ?>
                Ubmod.app.loadChart('<?php echo $userId ?>-storage-stacked-area',
                    'user', 'storageStackedArea', params);
            <?php endif; ?>
               <?php if ($interval['multi_month']): ?>
                Ubmod.app.loadChart('<?php echo $userId ?>-inodes-stacked-area',
                    'user', 'inodesStackedArea', params);
            <?php endif; ?>
        });
    </script>
    <div style="padding-top:5px;" class="labelHeading">
      User: <span class="labelHeader"><?php echo $user['name'] ?></span> &nbsp;&nbsp;
    </div>
    <div style="padding:5px; margin-bottom:20px; margin-top:10px;">
      <div class="chart-desc" style="font-weight:bold;">
        User Statistics
      </div>
      <table class="dtable">
        <tr>
          <th>Name:</th>
          <td style="font-weight:bold;"><?php echo $user['name'] ?></td>
          <th> Avg. Space Used (MB):</th>
          <td style="font-weight:bold;"><?php echo $user['avg_space_used'] ?></td>
          <th>Avg. Space Quota (MB):</th>
          <td style="font-weight:bold;"><?php echo $user['avg_space_quota'] ?></td>
          <th>Avg. Space Available (MB):</th>
          <td style="font-weight:bold;"><?php echo $user['avg_space_available'] ?></td>
          <th>Max. Space Used(MB):</th>
          <td style="font-weight:bold;"><?php echo $user['max_space_used'] ?></td>
        </tr>
        <tr>
          <th>Group:</th>
          <td style="font-weight:bold;"><?php echo $user['group'] ?></td>
          <th>Avg. Inodes Used:</th>
          <td style="font-weight:bold;"><?php echo $user['avg_inodes_used'] ?></td>
          <th>Avg. Inodes Available:</th>
          <td style="font-weight:bold;"><?php echo $user['avg_inodes_available'] ?></td>
          <th>Avg. Inodes Quota:</th>
          <td style="font-weight:bold;"><?php echo $user['avg_inodes_quota'] ?></td>
          <th>Max. Inodes Used:</th>
          <td style="font-weight:bold;"><?php echo $user['max_inodes_used'] ?></td>
        </tr>
      </table>
      <?php if ($interval['multi_month']): ?>
      <div style="margin-top:10px;"><img id="<?php echo $userId ?>-storage-stacked-area" src="<?php echo $BASE_URL ?>/images/loading.gif" /></div>
        <div style="margin-top:10px;"><img id="<?php echo $userId ?>-inodes-stacked-area" src="<?php echo $BASE_URL ?>/images/loading.gif" /></div>
      <?php endif; ?>
    </div>
  <?php else: ?>
    No storage data found for user in given time period.
  <?php endif; ?>
</div>

