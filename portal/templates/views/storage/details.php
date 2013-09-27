<div style="padding:10px;">
  <?php if ($user): ?>
    <script type="text/javascript">
        Ext.onReady(function () {
            var params = <?php echo $params ?>;

            Ubmod.app.extendPanelHeight();

            <?php if ($interval['multi_month']): ?>
                Ubmod.app.loadChart('<?php echo $userId ?>-stacked-area',
                    'user', 'stackedArea', params);
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
          <th> Avg. Space Used ():</th>
          <td style="font-weight:bold;"><?php echo $user['avg_space_used'] ?></td>
          <th>Avg. Space Quota ():</th>
          <td style="font-weight:bold;"><?php echo $user['avg_space_quota'] ?></td>
          <th>Avg. Inodes Used(CPUs):</th>
          <td style="font-weight:bold;"><?php echo $user['avg_inodes_used'] ?></td>
          <th>Avg. Inodes Quota ():</th>
          <td style="font-weight:bold;"><?php echo $user['avg_inodes_quota'] ?></td>
        </tr>
        <tr>
          <th>Group:</th>
          <td style="font-weight:bold;"><?php echo $user['group'] ?></td>
          <th> Avg. Space Used ():</th>
          <td style="font-weight:bold;"><?php echo $user['avg_space_used'] ?></td>
          <th>Avg. Space Quota ():</th>
          <td style="font-weight:bold;"><?php echo $user['avg_space_quota'] ?></td>
          <th>Avg. Inodes Used(CPUs):</th>
          <td style="font-weight:bold;"><?php echo $user['avg_inodes_used'] ?></td>
          <th>Avg. Inodes Quota ():</th>
          <td style="font-weight:bold;"><?php echo $user['avg_inodes_quota'] ?></td>
        </tr>
      </table>
      <?php if ($interval['multi_month']): ?>
        <div style="margin-top:10px;"><img id="<?php echo $userId ?>-stacked-area" src="<?php echo $BASE_URL ?>/images/loading.gif" /></div>
      <?php endif; ?>
    </div>
  <?php else: ?>
    No job data found for user in given time period.
  <?php endif; ?>
</div>

