<table class="table table-hover table-striped table-bordered table-membership">
    <?php
    echo '<thead>';
    echo '<tr>';

    echo '<th>SL.No.</th>';
    echo '<th>' . $data['tableGroupName'] . '</th>';
    if (in_array($groupId, [4, 5, 6])) {
        echo '<th>Entered</th>';
    }
    if (in_array($groupId, [2, 3, 4, 5, 6])) {
        echo '<th>Approved by Sub. Dist.</th>';
    }
//                                            echo '<!<th>Checked</th>';
    echo '<th>Approved by Rev. Dist.</th>';
    echo '<th>Approved by State</th>';
//                                         echo   '<th>Total</th>';

    echo '</tr>';
    echo '</thead>';
    echo ' <tbody>';

    foreach ($data['data'] as $k => $row) {
        echo '<tr>';
        echo '<td><a href="javascript:void(0)">' . ($k + 1) . '</a></td>';
        echo '<td><a class="office-name" href="javascript:void(0)" data-year='.$year.' data-group="' . $data['group'] . '" data-office="' . $row['id'] . '" class="office_name">' . $row['name'] . '</a></td>';
        if (in_array($groupId, [4, 5, 6])) {
            echo '<td><a target="_blank" href="' . base_url('membership/teacher?group=' . $data['group'] . '&office=' . $row['id'] . '&year=' . $year . '&view=1') . '">' . $row['entered'] . '</a></td>';
        }
        if (in_array($groupId, [2, 3, 4, 5, 6])) {
            echo '<td><a target="_blank" href="' . base_url('membership/teacher?group=' . $data['group'] . '&office=' . $row['id'] . '&year=' . $year . '&view=2') . '">' . $row['confirmed'] . '</a></td>';
        }
//                                        echo '<td><a target="_blank" href="' . base_url('membership/teacher?group=' . $group . '&office=' . $row['id'] . '&view=6') . '">' . $row['checked'] . '</a></td>';
        echo '<td><a target="_blank" href="' . base_url('membership/teacher?group=' . $data['group'] . '&office=' . $row['id'] . '&year=' . $year . '&view=3') . '">' . $row['verified'] . '</a></td>';
        echo '<td><a target="_blank" href="' . base_url('membership/teacher?group=' . $data['group'] . '&office=' . $row['id'] . '&year=' . $year . '&view=4') . '">' . $row['approved'] . '</a></td>';
//                                        echo '<td>' . $tlRow . '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '<tfoot>';
    echo '<tr class="bg-light-blue-gradient">';
    echo '<td></td>';
    echo '<td>' . $data['groupName'] . '</td>';
    if (in_array($groupId, [4, 5, 6])) {
        echo '<td><a class="" target="_blank" href="' . base_url('membership/teacher?view=1&year=' . $year . $data['tableFootUrl']) . '">' . $data['total']['tlEntered'] . '</a></td>';
    }
    if (in_array($groupId, [2, 3, 4, 5, 6])) {
        echo '<td><a class="" target="_blank" href="' . base_url('membership/teacher?view=2&year=' . $year . $data['tableFootUrl']) . '">' . $data['total']['tlConfirmed'] . '</a></td>';
    }
    echo '<td><a class="" target="_blank" href="' . base_url('membership/teacher?view=3&year=' . $year . $data['tableFootUrl']) . '">' . $data['total']['tlVerified'] . '</a></td>';
    echo '<td><a class="" target="_blank" href="' . base_url('membership/teacher?view=4&year=' . $year . $data['tableFootUrl']) . '">' . $data['total']['tlApproved'] . '</a></td>';
//                                        <!--<td>  // echo $total  </td>-->
    echo '</tr>';

    echo '</tfoot>';
    ?>
</table>