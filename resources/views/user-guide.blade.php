<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Leave Management System — User Guide</title>
    <style>
        @page { margin: 70px 60px; }

        body {
            font-family: "DejaVu Sans", sans-serif;
            color: #1a1a1a;
            font-size: 12px;
            line-height: 1.5;
        }

        h1 {
            color: #1d4ed8;
            font-size: 26px;
            margin-bottom: 4px;
        }

        .subtitle {
            color: #52525b;
            font-size: 13px;
            margin-bottom: 30px;
        }

        h2 {
            color: #1d4ed8;
            font-size: 16px;
            border-bottom: 2px solid #dbeafe;
            padding-bottom: 4px;
            margin-top: 28px;
        }

        h3 {
            font-size: 13px;
            color: #1a1a1a;
            margin-bottom: 4px;
            margin-top: 16px;
        }

        p {
            margin: 6px 0;
        }

        ul, ol {
            margin: 6px 0;
            padding-left: 20px;
        }

        li {
            margin-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 16px 0;
        }

        th, td {
            border: 1px solid #dbeafe;
            padding: 6px 8px;
            text-align: left;
            font-size: 11px;
        }

        th {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .note {
            background: #eff6ff;
            border-left: 4px solid #2563eb;
            padding: 8px 12px;
            margin: 10px 0;
            font-size: 11px;
        }

        .role-tag {
            display: inline-block;
            background: #dbeafe;
            color: #1d4ed8;
            padding: 1px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #a1a1aa;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Leave Management System</h1>
    <p class="subtitle">User Guide</p>

    <h2>1. Overview</h2>
    <p>
        This system replaces the paper "Leave Application" form. Every leave request goes
        through the same two stages, whether it's submitted on the web or via the mobile app:
    </p>
    <table>
        <tr>
            <th>Stage</th>
            <th>Who acts</th>
            <th>Result</th>
        </tr>
        <tr>
            <td>1. Submitted</td>
            <td>Employee</td>
            <td>Status: <em>Pending</em></td>
        </tr>
        <tr>
            <td>2. HOD decision</td>
            <td>Head of Division/Department (Manager)</td>
            <td>Status: <em>Approved</em> or <em>Rejected</em></td>
        </tr>
    </table>
    <div class="note">
        You are not allowed to proceed on leave until your request has been Approved. A request
        can also be <em>Cancelled</em> by the employee themselves, at any time before their HOD
        has decided on it.
    </div>

    <h2>2. Logging In</h2>
    <p>
        Go to the site's login page and enter your email and password. If you've forgotten
        your password, use the "Forgot your password?" link to receive a reset email. The same
        login works on the <a href="#mobile-app">mobile app</a>.
    </p>

    <h2>3. For Employees <span class="role-tag">All staff</span></h2>

    <h3>3.1 Submitting a leave request</h3>
    <p>From "My Requests", click <strong>New Request</strong> and choose a type:</p>
    <ul>
        <li><strong>Annual</strong> — the full form, matching the paper application: leave
            destination, address, and phone contact while away; your level and leave
            package; and any travel expense assistance.</li>
        <li><strong>Sick</strong>, <strong>Unpaid</strong>, <strong>Compassionate</strong>, or
            <strong>Maternity</strong> — just the dates and an optional reason.</li>
    </ul>
    <p>Fill in the start and end dates, and a reason if you'd like. You can also attach
        <strong>supporting documents</strong> — a photo or PDF, e.g. a medical certificate —
        right on the same form (on the mobile app you can take a photo directly with your
        phone's camera, or choose an existing file). Submit — your request starts at
        <strong>Pending</strong>, awaiting your HOD's decision.</p>

    <h3>3.2 Editing or cancelling a request</h3>
    <p>
        While a request is still <strong>Pending</strong> (your HOD hasn't decided on it yet),
        you can <strong>Edit</strong> it to change any of the details, or <strong>Cancel</strong>
        it if you no longer need the leave. Once your HOD has approved or rejected it, the
        request is final and can no longer be edited or cancelled. Your HOD is shown a notice
        the next time they check their Team Requests if you cancel one of their pending
        approvals.
    </p>

    <h3>3.3 Tracking your requests</h3>
    <p>
        "My Requests" lists everything you've submitted with its current status. Click
        <strong>View</strong> on any request to see the full application, including your HOD's
        decision and comments once recorded, and any supporting documents attached to it. You
        can add further documents to a request at any time from this page.
    </p>

    <h3>3.4 Your leave balance</h3>
    <p>
        The top of "My Requests" shows your remaining balance (31 annual days per year,
        minus days already approved) and your leave package amount, based on your level.
    </p>

    <h2>4. For Heads of Division/Department <span class="role-tag">Manager role</span></h2>
    <p>
        "Team Requests" lists your direct reports' leave requests, including any documents
        they've attached. Click <strong>Review</strong> on a request that's still
        <em>Pending</em> to record your decision:
    </p>
    <ul>
        <li><strong>Leave Relief Required</strong> — Yes or No, i.e. does someone need to
            cover this person's duties while they're away</li>
        <li><strong>Comments</strong> — optional notes</li>
        <li><strong>Approved</strong> or <strong>Not Approved</strong> — this is the final
            decision; the employee sees it immediately on their own request</li>
    </ul>
    <div class="note">
        If an employee cancels a request you haven't decided on yet, you'll see a notice the
        next time you open Team Requests.
    </div>

    <h2>5. Dashboard</h2>
    <p>Available to everyone, on both the web and mobile app, the Dashboard gives an
        at-a-glance view of leave across the organization:</p>
    <ul>
        <li><strong>On Leave Today</strong> — who's currently out</li>
        <li><strong>Leave by Type / Leave by Status</strong> — simple charts showing the
            overall spread of requests</li>
        <li><strong>Upcoming Leave</strong> — approved leave that hasn't started yet,
            soonest first</li>
    </ul>
    <p>
        The web Dashboard also shows a full calendar grid for the current month, with a badge
        on each day showing how many people are on approved leave — hover a badge to see who.
        The mobile app has an equivalent <strong>Calendar</strong> screen with month
        navigation; tap a highlighted date to see who's away that day.
    </p>

    <h2 id="mobile-app">6. Mobile App</h2>
    <p>
        The mobile app covers everything in this guide from your phone, using the same login
        as the web site. Ask your administrator for the current download link, or find it on
        the web app's dashboard. On Android, you'll need to allow "install from unknown
        sources" the first time, since the app isn't distributed through the Play Store.
    </p>
    <p>A few things work a little differently on mobile:</p>
    <ul>
        <li><strong>Offline mode</strong> — your leave balance and history are cached on the
            device, so you can still view them without a connection. A new request submitted
            while offline (without attachments) is saved as a draft and sent automatically
            once you're back online.</li>
        <li><strong>Notifications</strong> — the app shows a one-time alert when one of your
            requests changes status (for employees) or when a team member cancels a pending
            request (for HODs), each time you open the relevant screen since it last happened.</li>
        <li><strong>Camera capture</strong> — when attaching a supporting document, you can
            take a photo directly instead of choosing an existing file.</li>
    </ul>

    <h2>7. Your Profile</h2>
    <p>
        Under your name in the top-right, go to <strong>Profile</strong> to update your
        name, email, or password, and your TPF number, position, and duty station — the
        details that appear on your leave applications.
    </p>

    <h2>8. Request Statuses at a Glance</h2>
    <table>
        <tr>
            <th>Status</th>
            <th>Meaning</th>
        </tr>
        <tr>
            <td>Pending</td>
            <td>Submitted, waiting for your HOD to decide — can still be edited or cancelled</td>
        </tr>
        <tr>
            <td>Approved</td>
            <td>Final decision made — you're cleared to go on leave</td>
        </tr>
        <tr>
            <td>Rejected</td>
            <td>Final decision made — the request was not approved</td>
        </tr>
        <tr>
            <td>Cancelled</td>
            <td>Withdrawn by the employee before their HOD decided on it</td>
        </tr>
    </table>

    <div class="footer">Leave Management System — User Guide</div>
</body>
</html>
