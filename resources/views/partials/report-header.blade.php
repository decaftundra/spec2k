<!-- Header -->
<table class="table table-condensed header">
    <tr>
        <td><strong>Notification:</strong></td>
        <td>{{ $infoTable['notificationId'] }}</td>
        <td><strong>Material Receipt Date:</strong></td>
        <td>{{ $infoTable['RCS_MRD'] }}</td>
    </tr>
    <tr>
        <td><strong>Reporting Organisation Name:</strong></td>
        <td>{{ $infoTable['HDR_RON'] }}</td>
        <td><strong>Company Name:</strong></td>
        <td>{{ $infoTable['HDR_WHO'] }}</td>
    </tr>
    
    <tr>
        <td><strong>Part Number:</strong></td>
        <td>{{ $infoTable['RCS_MPN'] }}</td>
        <td><strong>Serial Number:</strong></td>
        <td>{{ $infoTable['RCS_SER'] }}</td>
    </tr>
</table>