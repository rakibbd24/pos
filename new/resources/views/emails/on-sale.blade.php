<!-- resources/views/emails/custom.blade.php -->

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>{{$data['subject']}}</title>
</head>

<body>
    <div class="email_body_style">
        <h1>
            <span>Dear {{ $data['client'] }},</span>
        </h1>
        <p style="color:rgb(17,24,39);font-size:16px;">Thank you for your purchase! Your invoice number is {{ $data['invoice_id'] }}.</p>
        <p style="color:rgb(17,24,39);font-size:16px;">If you have any questions or concerns, please don't hesitate to reach out to us. We are here to help!</p>
        {{-- <p style="color:rgb(17,24,39);font-size:16px;text-algn:center;">
            <a href="{{ $data['file'] }}" download="{{ rand(1111,9999).'.pdf' }}">Download Invoice</a>
        </p> --}}
        <p style="color:rgb(17,24,39);font-size:16px;">Best regards,</p><p style="color:rgb(17,24,39);font-size:16px;"><span>{{$data['company_name']}}</span></p>

      <p class="footer_email">
        &copy; <?php echo date ('Y'); ?>  {{$data['company_name']}}. {{ __('translate.All rights reserved') }}</p>
    </div>
</body>

</html>
