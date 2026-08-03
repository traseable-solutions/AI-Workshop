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
        This system replaces the paper "Annual Leave Application" form. Every leave request
        goes through the same three stages, whether it is submitted on the web or via the
        mobile app:
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
            <td>2. HOD recommendation</td>
            <td>Head of Division/Department (Manager)</td>
            <td>Status: <em>Awaiting PS</em></td>
        </tr>
        <tr>
            <td>3. Final decision</td>
            <td>Permanent Secretary</td>
            <td>Status: <em>Approved</em> or <em>Rejected</em></td>
        </tr>
    </table>
    <div class="note">
        You are not allowed to proceed on leave until your request has been Approved.
    </div>

    <h2>2. Logging In</h2>
    <p>
        Go to the site's login page and enter your email and password. If you've forgotten
        your password, use the "Forgot your password?" link to receive a reset email.
    </p>

    <h2>3. For Employees <span class="role-tag">All staff</span></h2>

    <h3>3.1 Submitting a leave request</h3>
    <p>From "My Requests", click <strong>New Request</strong> and choose a type:</p>
    <ul>
        <li><strong>Annual</strong> — the full form, matching the paper application: leave
            destination, address, and phone contact while away; your level and leave
            package; and any travel expense assistance.</li>
        <li><strong>Sick</strong> or <strong>Unpaid</strong> — just the dates and an
            optional reason.</li>
    </ul>
    <p>Fill in the start and end dates, and a reason if you'd like. Submit — your request
        starts at <strong>Pending</strong>, awaiting your HOD's recommendation.</p>

    <h3>3.2 Tracking your requests</h3>
    <p>
        "My Requests" lists everything you've submitted with its current status. Click
        <strong>View</strong> on any request to see the full application, including the
        HOD's recommendation and the Permanent Secretary's decision once recorded.
    </p>

    <h3>3.3 Your leave balance</h3>
    <p>
        The top of "My Requests" shows your remaining balance (31 annual days per year,
        minus days already approved) and your leave package amount, based on your level.
    </p>

    <h2>4. For Heads of Division/Department <span class="role-tag">Manager role</span></h2>
    <p>
        "Team Requests" lists your direct reports' leave requests. Click <strong>Review</strong>
        on a request that's still <em>Pending</em> to record your recommendation:
    </p>
    <ul>
        <li><strong>Leave Recommended</strong> — Yes or No</li>
        <li><strong>Leave Relief Required</strong> — Yes or No, i.e. does someone need to
            cover this person's duties while they're away</li>
        <li><strong>Comments</strong> — optional notes</li>
    </ul>
    <p>
        Submitting moves the request to <strong>Awaiting PS</strong> and forwards it to the
        Permanent Secretary — this happens regardless of whether you recommended it, exactly
        as on the paper form.
    </p>

    <h2>5. For the Permanent Secretary <span class="role-tag">PS role</span></h2>
    <p>
        "PS Approvals" lists every request an HOD has reviewed. Click <strong>Decide</strong>
        on a request that's <em>Awaiting PS</em> and choose <strong>Approved</strong> or
        <strong>Not Approved</strong>. This is the final decision — the employee sees it
        immediately on their own request.
    </p>

    <h2>6. Dashboard</h2>
    <p>Available to everyone, the Dashboard gives an at-a-glance view of leave across the
        organization:</p>
    <ul>
        <li><strong>On Leave Today</strong> — who's currently out</li>
        <li><strong>This month's calendar</strong> — a badge on each day shows how many
            people are on approved leave; hover a badge to see who</li>
        <li><strong>Leave by Type / Leave by Status</strong> — simple charts showing the
            overall spread of requests</li>
        <li><strong>Upcoming Leave</strong> — approved leave that hasn't started yet,
            soonest first</li>
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
            <td>Submitted, waiting for your HOD to review it</td>
        </tr>
        <tr>
            <td>Awaiting PS</td>
            <td>HOD has recorded a recommendation; waiting on the Permanent Secretary</td>
        </tr>
        <tr>
            <td>Approved</td>
            <td>Final decision made — you're cleared to go on leave</td>
        </tr>
        <tr>
            <td>Rejected</td>
            <td>Final decision made — the request was not approved</td>
        </tr>
    </table>

    <div class="footer">Leave Management System — User Guide</div>
</body>
</html>
