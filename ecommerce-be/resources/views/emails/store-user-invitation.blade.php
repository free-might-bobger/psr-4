<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Store Invitation – My Near Shops</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f5f7;font-family:'Segoe UI',Helvetica,Arial,sans-serif;">

  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f5f7;padding:40px 16px;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;width:100%;border-radius:20px;overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,0.10);">

          <!-- Header / Hero -->
          <tr>
            <td style="background:linear-gradient(135deg,#1e1b4b 0%,#312e81 55%,#4c1d95 100%);padding:48px 40px 40px;text-align:center;">

              <!-- Logo / Icon -->
              <div style="display:inline-block;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);border-radius:16px;padding:14px 18px;margin-bottom:24px;">
                <span style="font-size:28px;">🏪</span>
              </div>

              <h1 style="margin:0 0 8px;font-size:26px;font-weight:900;color:#ffffff;letter-spacing:-0.5px;line-height:1.2;">
                You've Been Invited!
              </h1>
              <p style="margin:0;font-size:15px;color:rgba(255,255,255,0.65);font-weight:500;">
                Store access invitation from <strong style="color:rgba(255,255,255,0.9);">{{ $storeUser['storeName'] }}</strong>
              </p>
            </td>
          </tr>

          <!-- Body -->
          <tr>
            <td style="background:#ffffff;padding:40px;">

              <!-- Greeting -->
              <p style="margin:0 0 8px;font-size:13px;font-weight:800;color:#9ca3af;text-transform:uppercase;letter-spacing:1.5px;">
                Hello there 👋
              </p>
              <h2 style="margin:0 0 20px;font-size:22px;font-weight:900;color:#111827;line-height:1.3;">
                {{ $storeUser['storeName'] }} has granted you access to their store on My Near Shops.
              </h2>
              <p style="margin:0 0 32px;font-size:15px;color:#6b7280;line-height:1.7;">
                You have been invited to collaborate and manage products on <strong style="color:#374151;">{{ $storeUser['storeName'] }}</strong>'s store.
                Click the button below to accept the invitation and get started.
              </p>

              <!-- Divider -->
              <div style="height:1px;background:linear-gradient(90deg,transparent,#e5e7eb,transparent);margin-bottom:32px;"></div>

              <!-- CTA Button -->
              <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td align="center">
                    <a href="{{ $storeUser['verification_code'] }}"
                       style="display:inline-block;background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%);color:#ffffff;text-decoration:none;font-size:16px;font-weight:800;padding:16px 48px;border-radius:14px;letter-spacing:0.3px;box-shadow:0 6px 20px rgba(99,102,241,0.35);">
                      ✓ &nbsp; Accept Invitation
                    </a>
                  </td>
                </tr>
              </table>

              <!-- Fallback link -->
              <p style="margin:28px 0 0;font-size:13px;color:#9ca3af;text-align:center;line-height:1.6;">
                If the button doesn't work, copy and paste this link into your browser:
              </p>
              <p style="margin:6px 0 0;font-size:12px;text-align:center;">
                <a href="{{ $storeUser['verification_code'] }}"
                   style="color:#4f46e5;word-break:break-all;text-decoration:none;">
                  {{ $storeUser['verification_code'] }}
                </a>
              </p>

              <!-- Divider -->
              <div style="height:1px;background:linear-gradient(90deg,transparent,#e5e7eb,transparent);margin:32px 0;"></div>

              <!-- Notice box -->
              <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td style="background:#f5f3ff;border:1px solid #ddd6fe;border-radius:12px;padding:16px 20px;">
                    <p style="margin:0;font-size:13px;color:#5b21b6;line-height:1.6;">
                      <strong>⚠️ Did not expect this?</strong><br />
                      If you weren't expecting an invitation, you can safely ignore this email.
                      Your account will not be affected.
                    </p>
                  </td>
                </tr>
              </table>

            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="background:#f9fafb;border-top:1px solid #f0f1f3;padding:24px 40px;text-align:center;">
              <p style="margin:0 0 4px;font-size:14px;font-weight:800;color:#374151;">My Near Shops</p>
              <p style="margin:0;font-size:12px;color:#9ca3af;">
                This invitation was sent automatically. Please do not reply to this email.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>

</body>
</html>