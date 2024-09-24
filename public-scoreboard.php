<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0, shrink-to-fit=no" />

	<!-- Recommended Security Headers -->
	<meta http-equiv="X-Content-Type-Options" content="nosniff">
	<meta http-equiv="X-XSS-Protection" content="1; mode=block">
	<meta name="referrer" content="no-referrer">
	<meta http-equiv="Strict-Transport-Security" content="max-age=31536000; includeSubDomains">
	<meta http-equiv="Access-Control-Allow-Methods" content="GET">
	<meta http-equiv="Feature-Policy" content="geolocation 'none'; midi 'none'; camera 'none'; usb 'none'">

	<title>Leaderboard | CDAC CTF Challenge</title>

	<!-- Favicon -->
	<link rel="icon" type="image/x-icon" href="/assets/img/favicon.ico" />

	<!-- Stylesheets -->
	<link rel="stylesheet" href="/assets/vendor/libs/normalize.css/normalize.css">
</head>

<body>

	<style>
		@import url("https://fonts.googleapis.com/css?family=DM+Sans:400,700&display=swap");

		html {
			--black: 0;
			--white: 255;
			--theme: var(--black);
			--theme-invert: var(--white);
			--base-full: rgba(var(--theme), var(--theme), var(--theme), 1);
			--base-80: rgba(var(--theme), var(--theme), var(--theme), 0.8);
			--base-60: rgba(var(--theme), var(--theme), var(--theme), 0.6);
			--base-40: rgba(var(--theme), var(--theme), var(--theme), 0.4);
			--base-20: rgba(var(--theme), var(--theme), var(--theme), 0.2);
			--base-10: rgba(var(--theme), var(--theme), var(--theme), 0.1);
			--base-5: rgba(var(--theme), var(--theme), var(--theme), 0.05);
			--invert-full: rgba(var(--theme-invert), var(--theme-invert), var(--theme-invert), 1);
			--invert-80: rgba(var(--theme-invert), var(--theme-invert), var(--theme-invert), 0.8);
			--invert-60: rgba(var(--theme-invert), var(--theme-invert), var(--theme-invert), 0.6);
			--invert-40: rgba(var(--theme-invert), var(--theme-invert), var(--theme-invert), 0.4);
			--invert-20: rgba(var(--theme-invert), var(--theme-invert), var(--theme-invert), 0.2);
			--invert-10: rgba(var(--theme-invert), var(--theme-invert), var(--theme-invert), 0.1);
			--invert-5: rgba(var(--theme-invert), var(--theme-invert), var(--theme-invert), 0.05);
			--red: #EE3F46;
			--blue: #00A0F5;
			--green: #27B768;
			--first: #F5CD75;
			--second: var(--base-60);
			--third: #C6906B;
		}

		html.theme--dark {
			--theme: var(--white);
			--theme-invert: var(--black);
		}

		html {
			box-sizing: border-box;
			font-size: 62.5%;

			-webkit-user-select: none;
			/* Safari */
			-moz-user-select: none;
			/* Firefox */
			-ms-user-select: none;
			/* IE10+/Edge */
			user-select: none;
			/* Standard syntax */
		}

		/* *, *:before, *:after { */
		/* box-sizing: inherit; */
		/* } */

		html,
		body {
			width: 100%;
			height: 100%;
		}

		body {
			font-size: 1.6rem;
			font-family: "DM Sans", system-ui;
			background: var(--invert-full);
			color: var(--base-full);
			transition: all 100ms ease-out 0s;
		}

		input,
		select,
		button,
		textarea {
			font-family: inherit;
			color: inherit;
			background: transparent;
		}

		input:focus,
		input:active,
		select:focus,
		select:active,
		button:focus,
		button:active,
		textarea:focus,
		textarea:active {
			outline: 0;
		}

		h1 {
			font-size: 4.9rem;
		}

		h2 {
			font-size: 3.9rem;
		}

		h3 {
			font-size: 3.1rem;
		}

		h4 {
			font-size: 2.5rem;
		}

		h5 {
			font-size: 2.1rem;
		}

		h6 {
			font-size: 1.6rem;
		}

		h1,
		h2,
		h3,
		h4,
		h5,
		h6 {
			margin-top: 0;
			margin-bottom: 1.6rem;
		}

		small {
			font-size: 1.3rem;
		}

		p {
			line-height: 1.5;
		}

		.l-wrapper {
			width: 100%;
			max-width: 90vw;
			margin: auto;
			padding: 3.2rem 0.8rem;
		}

		.l-header {
			width: 100%;
			max-width: 500px;
			margin: auto;
			padding: 2.4rem 0.8rem 1.6rem;
			position: relative;
		}

		.l-footer {
			text-align: center;
			padding-top: 1.6rem;
		}

		.c-swipe-zone {
			position: fixed;
			bottom: 0;
			height: 100%;
			left: 0;
			right: 0;
			transform: translateY(-100px);
			background: transparent;
		}

		.c-overlay {
			position: fixed;
			top: 0;
			bottom: 0;
			right: 0;
			left: 0;
			background: var(--base-40);
			z-index: 50;
			cursor: pointer;
			transition: all 200ms ease-out 0s;
		}

		.c-table {
			width: 100%;
			border-spacing: 0;
		}

		.c-table__row {
			transition: all 200ms ease-out 0s;
		}

		.c-table__row:nth-of-type(even) .c-table__cell {
			background: var(--base-5);
		}

		.c-table__head {
			position: sticky;
			top: 0;
			z-index: 1;
			background-color: var(--invert-full);
		}

		.c-table__head-cell {
			text-align: left;
			padding: 0.8rem;
			font-size: 1.3rem;
			border-bottom: 1px solid var(--base-40);
			color: var(--base-60);
			position: sticky;
			top: 0;
		}

		.c-table__cell--rank {
			z-index: 2;
		}

		.c-table__cell {
			padding: 0.8rem;
		}

		.c-rank {
			display: inline-flex;
			border-radius: 50%;
			width: 3.2rem;
			height: 3.2rem;
			background: var(--base-20);
			color: var(--invert-full);
			align-items: center;
			justify-content: center;
			font-size: 1.4rem;
			position: relative;
			border: 2px solid var(--base-20);
		}

		.c-rank:before {
			content: "";
			position: absolute;
			top: 0;
			bottom: 0;
			left: 0;
			right: 0;
			opacity: 0.15;
			border-radius: 50%;
		}

		.c-rank--first {
			border-color: var(--first);
			color: var(--first);
			background: transparent;
		}

		.c-rank--first:before {
			background: var(--first);
		}

		.c-rank--second {
			border-color: var(--second);
			color: var(--second);
			background: transparent;
		}

		.c-rank--second:before {
			background: var(--second);
		}

		.c-rank--third {
			border-color: var(--third);
			color: var(--third);
			background: transparent;
		}

		.c-rank--third:before {
			background: var(--third);
		}

		.c-winner {
			padding: 1.6rem;
			margin-bottom: 3.2rem;
			display: flex;
			justify-content: space-between;
			align-items: center;
			border: 1px solid var(--first);
			border-radius: 0.8rem;
			position: relative;
		}

		.c-winner:before {
			content: "";
			position: absolute;
			top: 0;
			bottom: 0;
			left: 0;
			right: 0;
			opacity: 0.08;
			background: var(--first);
		}

		.c-winner__image {
			width: 4.8rem;
			height: 4.8rem;
			color: var(--first);
		}

		.c-winner__content {
			width: 100%;
			padding-left: 1.6rem;
		}

		.c-winner__badge {
			text-transform: uppercase;
			color: var(--first);
			font-weight: 700;
			letter-spacing: 0.05em;
		}

		.c-winner__info {
			display: flex;
		}

		.c-winner__title {
			margin-top: 0.8rem;
			margin-bottom: 0.8rem;
		}

		.c-winner__info-item:not(:last-of-type) {
			margin-right: 1.6rem;
		}

		.c-empty-state {
			text-align: center;
			width: 100%;
			padding: 4rem 1.6rem;
			background: var(--base-5);
			color: var(--base-40);
			transition: all 200ms ease-out 0s;
		}

		.c-empty-state__icon {
			-webkit-animation: loader 2s infinite linear;
			animation: loader 2s infinite linear;
		}

		.c-headline {
			display: flex;
			justify-content: space-between;
			align-items: center;
			padding-bottom: 1.6rem;
		}

		@media screen and (max-width: 768px) {
			.c-headline {
				flex-direction: column;
				text-align: center;
			}
		}

		.c-headline__title {
			margin-bottom: 0;
		}

		@media screen and (max-width: 768px) {
			.c-headline__title {
				margin-bottom: 1.6rem;
			}
		}

		.c-chip {
			font-size: 1.2rem;
			padding: 0.4rem 0.8rem;
			border-radius: 999px;
			background: var(--base-20);
			display: inline-block;
			font-weight: 400;
			color: white;
			border: 1px solid var(--base-20);
			position: relative;
		}

		.c-chip:after {
			content: "";
			position: absolute;
			top: 0;
			bottom: 0;
			left: 0;
			right: 0;
			background: var(--base-20);
			opacity: 0.12;
			border-radius: 999px;
		}

		.c-chip--primary {
			color: var(--base-full);
			border-color: var(--base-full);
			background: transparent;
		}

		.c-chip--primary:after {
			background: var(--base-full);
		}

		.c-chip--danger {
			color: var(--red);
			border-color: var(--red);
			background: transparent;
		}

		.c-chip--danger:after {
			background: var(--red);
		}

		.c-chip--success {
			color: var(--green);
			border-color: var(--green);
			background: transparent;
		}

		.c-chip--success:after {
			background: var(--green);
		}

		.c-chip--info {
			color: var(--blue);
			border-color: var(--blue);
			background: transparent;
		}

		.c-chip--info:after {
			background: var(--blue);
		}

		.c-chip--secondary {
			color: var(--base-60);
			border-color: var(--base-60);
			background: transparent;
		}

		.c-chip--secondary:after {
			background: var(--base-60);
		}

		.c-chip--invert {
			color: var(--invert-full);
			border-color: var(--invert-full);
			background: transparent;
		}

		.c-chip--invert:after {
			background: var(--invert-full);
		}

		.u-text--left {
			text-align: left !important;
		}

		.u-text--right {
			text-align: right !important;
		}

		.u-text--center {
			text-align: center !important;
		}

		.u-text--primary {
			color: var(--base-full) !important;
		}

		.u-bg--primary {
			color: var(--base-full) !important;
		}

		.u-text--danger {
			color: var(--red) !important;
		}

		.u-bg--danger {
			color: var(--red) !important;
		}

		.u-text--success {
			color: var(--green) !important;
		}

		.u-bg--success {
			color: var(--green) !important;
		}

		.u-text--info {
			color: var(--blue) !important;
		}

		.u-bg--info {
			color: var(--blue) !important;
		}

		.u-text--secondary {
			color: var(--base-60) !important;
		}

		.u-bg--secondary {
			color: var(--base-60) !important;
		}

		.u-text--invert {
			color: var(--invert-full) !important;
		}

		.u-bg--invert {
			color: var(--invert-full) !important;
		}

		@-webkit-keyframes loader {
			from {
				transform: rotate(0deg);
			}

			to {
				transform: rotate(359deg);
			}
		}

		@keyframes loader {
			from {
				transform: rotate(0deg);
			}

			to {
				transform: rotate(359deg);
			}
		}

		@-webkit-keyframes fadeIn {
			from {
				opacity: 0;
			}

			to {
				opacity: 1;
			}
		}

		@keyframes fadeIn {
			from {
				opacity: 0;
			}

			to {
				opacity: 1;
			}
		}

		.switch {
			position: relative;
			display: inline-block;
			width: 50px;
			height: 25px;
			margin: 0;
		}

		.switch input {
			opacity: 0;
			width: 0;
			height: 0;
		}

		.slider {
			position: absolute;
			cursor: pointer;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background-color: #000;
			border-radius: 25px;
			-webkit-transition: .4s;
			transition: .4s;
		}

		.slider:before {
			position: absolute;
			content: "";
			height: 21px;
			width: 21px;
			left: 2px;
			bottom: 2px;
			background-color: #fff;
			border-radius: 50%;
			-webkit-transition: .4s;
			transition: .4s;
		}

		input:checked+.slider {
			background-color: #4CAF50;
		}

		input:checked+.slider:before {
			-webkit-transform: translateX(25px);
			-ms-transform: translateX(25px);
			transform: translateX(25px);
		}

		nav {
			position: fixed;
			top: 0;
			right: 0;
			margin-top: 2px;
			margin-right: 32px;
		}

		.table-wrapper {
			height: calc(60vh - 60px);
			overflow-y: auto;
		}

		/* Custom scrollbar */
		.table-wrapper::-webkit-scrollbar {
			width: 6px;
			/* Set the width */
		}

		/* Track */
		.table-wrapper::-webkit-scrollbar-track {
			background: var(--invert-full);
		}

		/* Handle */
		.table-wrapper::-webkit-scrollbar-thumb {
			background: #888;
			border-radius: 3px;
		}

		/* Handle on hover */
		.table-wrapper::-webkit-scrollbar-thumb:hover {
			background: #555;
		}

		.footer {
			position: fixed;
			bottom: 0;
			right: 0;
			background-color: var(--invert-full);
			color: #fff;
			/* padding: 1 1 1 1; */
			/* margin: 1 1 1 1; */
			padding-bottom: 1px;
			margin-bottom: -15px;
		}

		.no-underline {
			text-decoration: none;
			color: #0000FF;
		}

		.no-underline:visited {
			color: #0000FF;
		}

		.no-underline:hover {
			color: #800080;
		}

		.no-underline:active {
			color: #0000FF;
		}

		.footer p {
			color: var(--base-60);
			padding-bottom: 1px;
			padding-right: 5px;
		}
	</style>

<div style="text-align: center;">
<a href="/">
	<img src="/assets/img/CDAC-CTF.png" style="height:120px; width: auto;">
</a>
</div>
	<nav>
		<label class="switch">
			<input type="checkbox" id="modeToggle">
			<span class="slider"></span>
		</label>
	</nav>

	<?php
	require_once $_SERVER["DOCUMENT_ROOT"] . "/common/variables.php";

	if ($t >= $start_time && $t <= $end_time) {
		$comp_state = 'going';
	} elseif ($t <= $start_time) {
		$comp_state = 'upcoming';
	} elseif ($t >= $end_time) {
		$comp_state = 'end';
	}

	if ($comp_state != 'upcoming') {
		echo '<div class="l-wrapper" id="wrapper"></div>';
	}

	if ($comp_state == 'upcoming') {
		echo '
	<style>
		.theme--dark {
			.container {
				box-shadow: 0 10px 20px rgba(255, 255, 255, 0.1);

			}
		}

		.container {
			position: fixed;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			width: 80%;
			max-width: 600px;
			background-color: var(--invert-full);
			padding: 40px;
			border-radius: 10px;
			box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
			text-align: center;
		}


		h1 {
			color: var(--base-full);
			margin-bottom: 20px;
			padding-bottom: 10px;
		}

		p {
			color: var(--base-full);
			margin-bottom: 10px;
			padding-bottom: 20px;
		}
	</style>

	<div class="container">
		<h1>Prepare for the CTF Challenge!</h1>
		<p>The countdown to the CTF kickoff is on! Stay tuned for an electrifying competition where your hacking prowess will be put to the test.</p>
		<p>The CTF has not started yet. The leaderboard will be displayed once it starts.</p>
	</div>
	';
	}
	?>
	<footer class="footer">
		<p>Last Update at <?php echo date("h:i:s A  d F, Y", time()); ?>, <a href="/" class="no-underline">Go Back to Home</a></p>
	</footer>


	<script>
		// Containers
		const wrapper = document.getElementById('wrapper');

		// Create Element
		const createNode = element => {
			return document.createElement(element);
		};

		// Append Element
		const append = (parent, el) => {
			return parent.appendChild(el);
		};


		// Render Empty State
		const emptyState = () => {
			const newText = createNode('div');
			newText.classList = 'c-empty-state';
			newText.innerHTML = `
		<svg class="c-empty-state__icon" viewBox="0 0 24 24" width="48" height="48" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
			<line x1="12" y1="2" x2="12" y2="6"></line>
			<line x1="12" y1="18" x2="12" y2="22"></line>
			<line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line>
			<line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line>
			<line x1="2" y1="12" x2="6" y2="12"></line>
			<line x1="18" y1="12" x2="22" y2="12"></line>
			<line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line>
			<line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line>
		</svg>
		<div style="margin-top: 8px;">Loading...</div>
	`;
			append(wrapper, newText);
			setTimeout(() => {
				newText.remove();
			}, 500);
		};

		const renderList = async (compStatus) => {
			try {
				const apiUrl = '/api/getScore';
				const response = await fetchData(apiUrl);
				const data = await response.json();

				const table = createTable();
				const title = createTitle(compStatus);
				const wrapper = document.getElementById('wrapper');
				emptyState(wrapper);

				append(wrapper, title);
				append(wrapper, table);

				data.forEach(item => {
					const row = createTableRow(item);
					const tableBody = table.querySelector('tbody');
					append(tableBody, row);

					if (item.position === 1 && compStatus !== 'going') {
						const firstRankCard = createWinnerCard(item);
						insertBefore(table.parentNode, firstRankCard, table);
					}
				});
			} catch (error) {
				console.error(error);
			}
		};

		const fetchData = async (url) => {
			return fetch(url, {
				method: 'POST',
			});
		};


		const createTable = () => {
			const tableClass = 'c-table';
			const table = createNode('table');
			table.classList = tableClass;
			table.innerHTML = `
        <thead class="c-table__head">
            <tr class="c-table__head-row">
                <th class="c-table__head-cell u-text--center">Rank</th>
                <th class="c-table__head-cell">Users</th>
                <th class="c-table__head-cell u-text--center">Last Solved</th>
                <th class="c-table__head-cell u-text--center">Challenge Solved</th>
                <th class="c-table__head-cell u-text--center">Total Points</th>
            </tr>
        </thead>
        <tbody></tbody>`;

			// Creating the wrapper div
			const wrapperDiv = createNode('div');
			wrapperDiv.classList.add('table-wrapper');
			wrapperDiv.appendChild(table);

			return wrapperDiv;
		};

		const createTitle = (compStatus) => {
			const titleClass = 'c-headline';
			const title = createNode('div');
			title.classList = titleClass;
			title.innerHTML = `<h4 class="${titleClass}__title"><small class="u-text--danger">CDAC CTF Challenge</small><br />Leaderboard</h4><span class="c-chip ${getChipClass(compStatus)}">${getSeasonLabel(compStatus)}</span>`;
			return title;
		};

		const getStatusLabel = (compStatus) => {
			return compStatus === 'going' ? new Date().getFullStatus() : compStatus;
		};

		const getSeasonLabel = (compStatus) => {
			return compStatus === 'going' ? 'CTF Competition Ongoing' : 'Competition Over';
		};

		const getChipClass = (compStatus) => {
			return compStatus === 'going' ? 'c-chip--success' : 'c-chip--secondary';
		};

		const createTableRow = (item) => {
			const rowClass = 'c-table__row';
			const row = createNode('tr');
			row.classList = rowClass;
			row.innerHTML = `
    <td class="c-table__cell c-table__cell--rank u-text--center"><span class="c-rank">${item.position}</span></td>
    <td class="c-table__cell c-table__cell--name">${item.userName}<br><small style="opacity: .4;">${item.email} (${item.status})</small></td>
    <td class="c-table__cell c-table__cell--count u-text--center">${item.time}</td>
    <td class="c-table__cell c-table__cell--count u-text--center">${item.solved}</td>
    <td class="c-table__cell c-table__cell--points u-text--center"><strong>${item.points}</strong></td>`;

			addRankClass(row.querySelector('.c-rank'), item.position);

			return row;
		};

		const addRankClass = (element, position) => {
			if (position === 1) {
				element.classList.add('c-rank--first');
			} else if (position === 2) {
				element.classList.add('c-rank--second');
			} else if (position === 3) {
				element.classList.add('c-rank--third');
			}
		};

		const createWinnerCard = (item) => {
			const cardClass = 'c-winner';
			const card = createNode('div');
			card.classList = cardClass;
			card.innerHTML = `
		<div class="${cardClass}__image">
			<svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
				<circle cx="12" cy="8" r="7"></circle>
				<polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
			</svg>
		</div>
		<div class="${cardClass}__content">
			<small class="${cardClass}__badge">1st Position (Winner)</small>
			<h5 class="${cardClass}__title">${item.userName}</h5>
			<div class="${cardClass}__info">
				<small class="${cardClass}__info-item"><strong>${item.email}</strong></small>
				<small class="${cardClass}__info-item">Challenge Solved: <strong>${item.solved}</strong></small>
				<small class="${cardClass}__info-item">Total Points: <strong>${item.points}</strong></small>
			</div>
		</div>`;
			return card;
		};

		const insertBefore = (parent, newNode, referenceNode) => {
			parent.insertBefore(newNode, referenceNode);
		};

		function toggleTheme() {
			const isDarkMode = document.documentElement.classList.contains('theme--dark');
			document.documentElement.classList.toggle('theme--dark', !isDarkMode);
			localStorage.setItem('templateCustomizer-vertical-menu-template--Style', isDarkMode ? 'light' : 'dark');
		}

		const modeToggle = document.getElementById('modeToggle');

		const savedTheme = localStorage.getItem('templateCustomizer-vertical-menu-template--Style');

		if (savedTheme === 'system') {
			if (window.matchMedia("(prefers-color-scheme: dark)").matches) {
				document.documentElement.classList.add('theme--dark');
				modeToggle.checked = true;
			}
		}

		if (savedTheme === 'dark') {
			document.documentElement.classList.add('theme--dark');
			modeToggle.checked = true;
		}

		modeToggle.addEventListener('change', toggleTheme);

		document.addEventListener('contextmenu', function(event) {
			event.preventDefault();
		});

		setTimeout(function() {
			location.reload();
		}, 120000);
	</script>

	<?php
	if ($comp_state != 'upcoming') {
		echo "<script type='text/javascript'>renderList('" . $comp_state . "')</script>";
	}
	?>

</body>

</html>