<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Style-Type" content="text/css" />
    <meta name="generator" content="Aspose.Words for .NET 25.8.0" />
    <title></title>
    <style type="text/css">
        body {
            line-height: 108%;
            font-family: Calibri;
            font-size: 11pt
        }

        p {
            margin: 0pt 0pt 8pt
        }
    </style>
</head>

<body>
    <div>
        <p style="margin-bottom:0pt; line-height:115%; font-size:12pt">
            <span style="font-family:SimSun; font-weight:bold; color:#1f1f1f; background-color:#ffffff">
                郵件標題：【立即啟用】您專屬交易武器已解鎖，請即刻查收！
            </span>
        </p>
        <p style="margin-bottom:0pt; line-height:115%; font-size:12pt">
            <span style="font-weight:bold; -aw-import:ignore">&#xa0;</span>
        </p>
        <p style="margin-bottom:0pt; line-height:115%; font-size:12pt">
            <span style="font-weight:bold">
                Email 
            </span>
            <span style="font-family:SimSun; font-weight:bold">
                內容：
            </span>
        </p>
        <p style="margin-top:10pt; margin-bottom:10pt; line-height:normal; font-size:12pt">
            <span style="font-family:SimSun; background-color:#ffffff">
                你好
            </span>
            <span style="background-color:#ffffff; -aw-import:spaces">&#xa0; </span>
            <span style="background-color:#ffffff">
                {{ $record->name }} ,
            </span>
        <p style="margin-top:10pt; margin-bottom:10pt; line-height:normal; font-size:12pt">
            <span style="font-family:SimSun; background-color:#ffffff">
                在此，我們向您提供了独特的序列號
            </span>
            <span style="background-color:#ffffff">
                 (Serial No.) 
            </span>
            <span style="font-family:SimSun; background-color:#ffffff">
                以及安裝步驟與操作說明指南
            </span>
        </p>
        <p style="margin-top:10pt; margin-bottom:10pt; line-height:normal; font-size:12pt">
            <span style="font-family:SimSun; background-color:#ffffff">
                然而，我們强調，這個序列號的保密性至關重要。
            </span>
        </p>
        <p style="margin-bottom:0pt; line-height:115%; font-size:12pt">
            <span style="-aw-import:ignore">&#xa0;</span>
        </p>
        <p style="margin-bottom:0pt; line-height:normal; font-size:12pt">
            <span style="font-weight:bold; font-style:italic; color:#ff0000; background-color:#ffffff">
                *
            </span>
            <span style="font-family:SimSun; font-weight:bold; font-style:italic; color:#ff0000; background-color:#ffffff">
                請務必不要與任何人分享此序列號
            </span>
            <span style="font-family:SimSun; font-style:italic; color:#ff0000; background-color:#ffffff">
                。
            </span>
        </p>
        <p style="margin-bottom:0pt; line-height:normal; font-size:12pt">
            <span style="font-family:SimSun; background-color:#ffffff">
                序列號是您使用產品的唯一憑證，如果這個序列號泄露出去，
            </span>
        </p>
        <p style="margin-bottom:0pt; line-height:normal; font-size:12pt">
            <span style="font-family:SimSun; background-color:#ffffff">
                可能会導致嚴重後果甚至需承擔法律責任。
            </span>
        </p>
        <p style="margin-bottom:0pt; line-height:normal; font-size:12pt">
            <span style="font-weight:bold; -aw-import:ignore">&#xa0;</span>
        </p>
        <p style="margin-bottom:0pt; line-height:normal; font-size:12pt">
            <span style="font-weight:bold; -aw-import:ignore">&#xa0;</span>
        </p>
        @foreach ($codes as $code)
            <p style="margin-bottom:0pt; line-height:115%; font-size:12pt">
                <span style="font-family:'Segoe UI Symbol'; font-weight:bold">✅</span>
                <span style="font-weight:bold">{{ $code['product_name'] }}</span>
                <span style="font-family:SimSun; font-weight:bold">序列號</span>
                <span style="font-weight:bold">(Serial No.)</span>
                <span style="font-family:SimSun; font-weight:bold">資料：</span>
            </p>
            <p style="margin-top:10pt; margin-bottom:10pt; line-height:normal; font-size:12pt">
                <span style="font-family:SimSun; background-color:#ffffff">序列號</span>
                <span style="background-color:#ffffff">(Serial No.) : {{ $code['serial_number'] }}</span>
            </p>
        @endforeach
        <p style="margin-top:10pt; margin-bottom:10pt; line-height:normal; font-size:12pt">
            <span style="-aw-import:ignore">&#xa0;</span>
        </p>
        <p style="margin-top:10pt; margin-bottom:10pt; line-height:normal; font-size:12pt">
            <span style="background-color:#ffffff">
                *
            </span>
            <span style="font-family:SimSun; background-color:#ffffff">
                截止日期為
            </span>
            <span style="background-color:#ffffff">
                {{ $record->expired_date }}
            </span>
            <span style="font-family:SimSun; background-color:#ffffff">
                (為期限1年)
            </span>
            <br />
            <span style="font-size:11pt; font-weight:bold; -aw-import:ignore">&#xa0;</span>
        </p>
        <p style="margin-bottom:0pt; line-height:115%">
            <span style="font-weight:bold; font-style:italic; color:#ff0000">
                *
            </span>
            <span style="font-family:SimSun; font-weight:bold; font-style:italic; color:#ff0000">
                再次提醒，注意：請保密此序列號，不要與任何人分享。
            </span>
        </p>
        <p style="margin-bottom:0pt; line-height:115%">
            <span style="-aw-import:ignore">&#xa0;</span>
        </p>
        <p style="margin-bottom:0pt; line-height:115%">
            <span style="font-family:SimSun">
                若有任何疑問或需要幫助，請隨時與您的專員聯係或者聯繫
            </span>
            <span>
                {{ $contactEmail }}
            </span>
        </p>
        <p style="margin-bottom:0pt; line-height:115%">
            <span style="font-family:SimSun">
                謝謝！
            </span>
        </p>
        <p style="margin-bottom:0pt; line-height:115%">
            <br />
            <br />
            <span style="font-family:SimSun; background-color:#ffffff">
                此致，
            </span>
        </p>
        <p style="margin-bottom:0pt; line-height:115%">
            <span style="background-color:#ffffff">
                Simmi Goh 
            </span>
            <span style="font-family:SimSun; background-color:#ffffff">
                團隊
            </span>
        </p>
    </div>
</body>

</html>