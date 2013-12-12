function getHTTPObject() {
    if (typeof XMLHttpRequest != 'undefined') {
        return new XMLHttpRequest();
    }
    try {
        return new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
        try {
            return new ActiveXObject("Microsoft.XMLHTTP");
        } catch (e) {}
    }
    return false;
};

function m(index, url, process_function) {
    xml_doc[index] = getHTTPObject();
    xml_doc[index].open("GET", url, true);
    xml_doc[index].onreadystatechange = function () {
        if (xml_doc[index].readyState == 4) {
            xml_doc[index] = xml_doc[index].responseXML;
            process_function();
        }
    };
    xml_doc[index].send(null);
};
var options_popup;
var popup_open = false;
var popup_timer;

function ar() {
    if (document.documentElement && document.documentElement.scrollTop) return (document.documentElement.scrollTop);
    else if (document.body) return (document.body.scrollTop);
};

function as() {
    if (document.documentElement && document.documentElement.scrollLeft) return (document.documentElement.scrollLeft);
    else if (document.body) return (document.body.scrollLeft);
};

function display_options_popup() {
    if (popup_open) {
        options_popup.style.visibility = 'hidden';
        popup_open = false;
    } else {
        options_popup.style.visibility = 'visible';
        popup_open = true;
    }
};

function am() {
    if (popup_open) {
        options_popup.style.visibility = 'hidden';
        popup_open = false;
    }
};

function set_options_popup_values() {
    game_gone_color = GetCookie("game_gone_color");
    changes_2_minutes = GetCookie("changes_2_minutes");
    changes_5_minutes = GetCookie("changes_5_minutes");
    changes_10_minutes = GetCookie("changes_10_minutes");
    if (game_gone_color == null) game_gone_color = "game-gone";
    if (changes_2_minutes == null) changes_2_minutes = "redbg";
    if (changes_5_minutes == null) changes_5_minutes = "greenbg";
    if (changes_10_minutes == null) changes_10_minutes = "yellowbg";
    document.getElementById("show_2_minute_color").className = changes_2_minutes + "-right";
    document.getElementById("show_5_minute_color").className = changes_5_minutes + "-right";
    document.getElementById("show_10_minute_color").className = changes_10_minutes + "-right";
    document.getElementById("show_game_gone_color").className = game_gone_color + "-left";
    if (game_gone_color == 'whitebg') document.getElementById("show_game_gone_color").style.color = '#FFFFFF';
    else document.getElementById("show_game_gone_color").style.color = '#000000';
    document.getElementById('game_gone_color').className = game_gone_color;
    document.getElementById('changes_2_minutes').className = changes_2_minutes;
    document.getElementById('changes_5_minutes').className = changes_5_minutes;
    document.getElementById('changes_10_minutes').className = changes_10_minutes;
};

function save_and_hide_popup() {
    if (popup_open) {
        options_popup.style.visibility = 'hidden';
        popup_open = false;
        game_gone_color = document.getElementById('game_gone_color').className;
        changes_2_minutes = document.getElementById('changes_2_minutes').className;
        changes_5_minutes = document.getElementById('changes_5_minutes').className;
        changes_10_minutes = document.getElementById('changes_10_minutes').className;
        document.getElementById("show_2_minute_color").className = changes_2_minutes + "-right";
        document.getElementById("show_5_minute_color").className = changes_5_minutes + "-right";
        document.getElementById("show_10_minute_color").className = changes_10_minutes + "-right";
        document.getElementById("show_game_gone_color").className = game_gone_color + "-left";
        if (game_gone_color == 'whitebg') document.getElementById("show_game_gone_color").style.color = '#FFFFFF';
        else document.getElementById("show_game_gone_color").style.color = '#000000';
        k("game_gone_color", game_gone_color, 5 * 365);
        k("changes_2_minutes", changes_2_minutes, 5 * 365);
        k("changes_5_minutes", changes_5_minutes, 5 * 365);
        k("changes_10_minutes", changes_10_minutes, 5 * 365);
        if (sport_name.length > 0) A();
    }
};

function cancel_and_hide_popup() {
    if (popup_open) {
        options_popup.style.visibility = 'hidden';
        popup_open = false;
        set_options_popup_values();
    }
};

function toggle_settings() {
   var ele = document.getElementById('settingsTable');
   if( ele.style.display == 'none')
   {
   		ele.style.display = '';
   }else
   {
   		ele.style.display = 'none';
   }
};

function select_color(minutes, class_name) {
    if (minutes == 0) document.getElementById('game_gone_color').className = class_name;
    else document.getElementById('changes_' + minutes + '_minutes').className = class_name;
};
var xml_doc = new Array(1);
var books;
var number_of_books;
var reverse_book = new Array();

function ac(id, name, link) {
    this.id = id;
    this.name = name;
    this.link = link;
};
var table_width = 1250;

function get_books() {
    m(0, books_url, M);
};

function M() {
    items = xml_doc[0].getElementsByTagName("BOOK");
    number_of_books = items.length;
    display_line = new Array(number_of_books + 1);
    books = new Object(number_of_books);
    for (i = 0; i < number_of_books; i++) {
        id = items[i].getAttribute("id");
        name = items[i].getAttribute("name");
        link = items[i].getAttribute("link");
        books[i] = new ac(id, name, link);
        reverse_book[id] = i;
    }
    af();
};
var sport_name;
var page_period;
var total_rows = 0;
var schedule = new Object();
var schedule_time = 0;
var changes_time = 0;
var clear_time = 0;
var game_lookup = new Object();
var timezone;
var timers = new Object();
var HEADER = 0;
var BOOKSH = 1;
var GAME = 2;

function j(type, league, value) {
    this.type = type;
    this.league = league;
    this.value = value;
};

function D(date) {
    year = date.substring(0, 4);
    month = date.substring(4, 6);
    day = date.substring(6);
    if (month.charAt(0) == '0') month = month.charAt(1);
    if (day.charAt(0) == '0') day = day.charAt(1);
    return (new Date(parseInt(year), parseInt(month) - 1, parseInt(day)));
};

function H(game, number, date, time) {
    var now = new Date();
    now_millis = now.getTime();
    var game_date = D(date);
    if (time.charAt(0) == '0') game_date.setHours(parseInt(time.substring(1, 2)) + timezone);
    else game_date.setHours(parseInt(time.substring(0, 2)) + timezone);
    game_date.setMinutes(parseInt(time.substring(2)) + 0);
    game_date.setSeconds(0);
    game_date.setMilliseconds(0);
    if (timers['g' + number] != null) window.clearTimeout(timers['g' + number]);
    game_millis = game_date.getTime();
    if (game_millis > now_millis) timers['g' + number] = setTimeout('I (' + number + ')', (game_millis - now_millis + 1000));
    year = game_date.getFullYear();
    month = game_date.getMonth() + 1;
    if (month < 10) month = '0' + month;
    day = game_date.getDate();
    if (day < 10) day = '0' + day;
    hours = game_date.getHours();
    if (hours < 10) hours = '0' + hours;
    minutes = game_date.getMinutes();
    if (minutes < 10) minutes = '0' + minutes;
    game.date = '' + year + month + day;
    game.time = '' + hours + minutes;
    return (game_date);
};

function ab(bgcolor, date, time, number, seconds, away, away_pitcher, away_hand, away_lines, home, home_pitcher, home_hand, home_lines, away_score, home_score, period, status) {
    this.bgcolor = bgcolor;
    H(this, number, date, time);
    this.number = number;
    this.seconds = seconds;
    this.team = new Object(2);
    this.pitcher = new Object(2);
    this.hand = new Object(2);
    this.lines = new Object(2);
    this.score = new Object(2);
    this.status = new Object(2);
    this.team[0] = away;
    this.pitcher[0] = away_pitcher;
    this.hand[0] = away_hand;
    this.lines[0] = away_lines;
    this.score[0] = away_score;
    this.status[0] = period;
    this.team[1] = home;
    this.pitcher[1] = home_pitcher;
    this.hand[1] = home_hand;
    this.lines[1] = home_lines;
    this.score[1] = home_score;
    this.status[1] = status;
    C(this);
};

function ao(status) {
    period = parseInt(status);
    last_digit = period % 10;
    if (period == 11 || period == 12 || period == 13) value = 'th';
    else if (last_digit == 1) value = 'st';
    else if (last_digit == 2) value = 'nd';
    else if (last_digit == 3) value = 'rd';
    else value = 'th';
    return (value);
};

function C(game) {
    if (game.score[0] == null || game.score[0] == '') game.score[0] = '';
    else game.score[0] = parseInt(game.score[0]);
    if (game.score[1] == null || game.score[1] == '') game.score[1] = '';
    else game.score[1] = parseInt(game.score[1]);
    if (game.status[0] == null) game.status[0] = '';
    if (game.status[1] == null) game.status[1] = '';
};

function Line(book_id, value, seconds, slide, total) {
    this.book_id = book_id;
    this.value = value;
    this.seconds = seconds;
	this.slide = slide;
	this.total = total;
};

function af() {
    timezone = 8 - V() - (((new Date()).getTimezoneOffset()) / 60);
    m(0, lines_url, T);
};

function T() {
    if (xml_doc[0] == null) node_value = null;
    else node_value = xml_doc[0].getElementsByTagName("ODDS");
    if (node_value != null && node_value.length > 0) {
        schedule_time = node_value[0].getElementsByTagName("SCHEDULE")[0].getAttribute("value");
        changes_time = node_value[0].getElementsByTagName("TIME")[0].getAttribute("value");
        var row = 0;
        var old_game = null;
        var game = null;
        var leagues = node_value[0].getElementsByTagName("LEAGUE");
        var number_of_leagues = leagues.length;
        if (number_of_leagues == 0) schedule[row++] = new j(HEADER, 0, 'No games found.');
        for (i = 0; i < number_of_leagues; i++) {
            league_number = leagues[i].getAttribute("number");
            league_name = leagues[i].getAttribute("name");
            var children = leagues[i].childNodes;
            schedule[row++] = new j(HEADER, league_number, league_name);
            var games_in_league = 0;
            bgcolor = 0;
            for (c = 0; c < children.length; c++) {
                var item = children[c];
                switch (item.nodeName) {
                case 'HEADER':
                    break;
                case 'GAME':
                    {
                        var game_date = item.getAttribute('date');
                        var game_time = item.getAttribute('time');
                        var game_seconds = item.getAttribute('seconds');
                        var away_score;
                        var home_score;
                        var period = '';
                        var status = '';
                        var teams = item.childNodes;
                        var number = '';
                        var away = '';
                        var home = '';
                        var away_lines;
                        var home_lines;
                        for (t = 0; t < teams.length; t++) {
                            var team = teams[t];
                            if (team.nodeName == 'TEAM') {
                                var line_values = new Object(number_of_books);
                                var lines = team.childNodes;
                                for (l = 0; l < number_of_books + 1; l++) line_values[l] = new Line(0, "", 9999, "", "");
                                for (l = 0; l < lines.length; l++) {
                                    if (lines[l].nodeName == 'OPENER') {
                                        line_values[0] = new Line(0, lines[l].getAttribute('value'), 9999, "", "");
                                    } else if (lines[l].nodeName == 'LINE') {
                                        book_id = parseInt(lines[l].getAttribute('book'));
                                        book = reverse_book[book_id];
                                        seconds = lines[l].getAttribute('seconds');
										slide = lines[l].getAttribute('slide');
										total = lines[l].getAttribute('total');
                                        if (seconds == null || seconds == '') seconds = '9999';
                                        line_values[book + 1] = new Line(book_id, lines[l].getAttribute('value'), parseInt(seconds), slide, total);
                                    }
                                }
                                if (number == '') {
                                    number = parseInt(team.getAttribute('number'));
                                    away_score = team.getAttribute('score');
                                    period = team.getAttribute('status');
                                    away = team.getAttribute('name');
                                    away_pitcher = team.getAttribute('pitcher');
                                    away_hand = team.getAttribute('hand');
                                    away_lines = line_values;
                                } else {
                                    home_score = team.getAttribute('score');
                                    status = team.getAttribute('status');
                                    home = team.getAttribute('name');
                                    home_pitcher = team.getAttribute('pitcher');
                                    home_hand = team.getAttribute('hand');
                                    home_lines = line_values;
                                }
                            }
                        }
                        old_game = game;
                        game = new ab(bgcolor, game_date, game_time, number, game_seconds, away, away_pitcher, away_hand, away_lines, home, home_pitcher, home_hand, home_lines, away_score, home_score, period, status);
                        var add_book_header = false;
                        if (old_game == null || game.date != old_game.date) {
                            if (schedule[row - 1].type == GAME) add_book_header = true;
                            schedule[row++] = new j(HEADER, league_number, ag(game.date));
                            bgcolor = 0;
                            game.bgcolor = bgcolor;
                            games_in_league = 0;
                        } else if (games_in_league >= 20) {
                            add_book_header = true;
                            schedule[row++] = new j(HEADER, league_number, '');
                            bgcolor = 0;
                            game.bgcolor = bgcolor;
                            games_in_league = 0;
                        }
                        if (add_book_header) schedule[row++] = new j(BOOKSH, league_number, '');
                        game_lookup[number] = row;
                        game_lookup[number + 1] = row;
                        schedule[row++] = new j(GAME, league_number, game);
                        bgcolor = (bgcolor + 1) % 2;
                        games_in_league++;
                    }
                    break;
                }
            }
        }
        total_rows = row;
        A();
    }
    interval = document.getElementById("changes_interval").value;
    changes_timer = setTimeout('v ()', interval * 1000);
    K();
};
var days_of_the_week = new Array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
var months = new Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");

function ag(string_date) {
    var date = D(string_date);
    year = date.getYear();
    if (year < 1900) year += 1900;
    the_date = date.getDate();
    suffix = 'th';
    mod10 = the_date % 10;
    if (the_date == 11 || the_date == 12 || the_date == 13) {
        suffix = 'th';
    } else if (mod10 == 1) suffix = 'st';
    else if (mod10 == 2) suffix = 'nd';
    else if (mod10 == 3) suffix = 'rd';
    return (days_of_the_week[date.getDay()] + ', ' + months[date.getMonth()] + ' ' + the_date + suffix + ', ' + year);
};

function aj(current, total) {
    odds_table.innerHTML = 'Processing record ' + current + ' of ' + total;
};

function clear_changes() {
    for (r = 0; r < total_rows; r++) {
        var item = schedule[r];
        if (item.type == GAME) {
            item = schedule[r].value;
            for (t = 0; t < 2; t++) {
                var team = item.team[t];
                var lines = item.lines[t];
                for (i = 0; i < number_of_books + 1; i++) {
                    lines[i].seconds = 9999;
                }
            }
        }
    }
    A();
    clear_time = (new Date()).getTime() / 1000;
    k("clear-time-" + sport_name + page_period, "" + clear_time, 30);
};

function A() {
    odds = '';
    d('<table id="schedule_table" width="' + table_width + '" border="0" bgcolor="#C1C1C1" cellspacing="1">');
    for (r = 0; r < total_rows; r++) {
        var item = schedule[r];
        if (item.type == HEADER) R(item.value);
        else if (item.type == BOOKSH);
        else {
            item = schedule[r].value;
            B(item.date, item.time, r);
            for (t = 0; t < 2; t++) {
                var team = item.team[t];
                var pitcher = item.pitcher[t];
                var hand = item.hand[t];
                var lines = item.lines[t];
                var team_number = item.number + t;
                G(team_number, team, pitcher, hand, item.score[t], item.status[t]);
                for (i = 0; i < number_of_books + 1; i++) {
                    f(true, team_number, i, lines[i].value, lines[i].seconds);
                }
            }
            L(item.bgcolor);
        }
    }
    aa();
    d('</table>');
    document.getElementById("odds_table").innerHTML = odds;
	if(parent.document.getElementById("lines_frame"))
	{
	  parent.auto_height();
	}
	
};

function toggle_game_gone() {
    var button = document.getElementById("toggle_game_gone_button");
    var show;
    if (button.value == "Hide started games") {
        button.value = "Show started games";
        show = false;
    } else {
        button.value = "Hide started games";
        show = true;
    }
    var expand = '';
    var all_rows = document.getElementById('schedule_table').rows;
    for (r = 0; r < total_rows; r++) {
        var item = schedule[r];
        var element = document.getElementById('expand' + r);
        if (element != null) expand = element.innerHTML;
        if (item.type == GAME) {
            if (show) {
                all_rows[r].style.display = "";
            } else {
                game = item.value;
                if (o(game.date, game.time)) all_rows[r].style.display = "none";
            }
        }
    }
};

function ah(row) {
    var expand = document.getElementById('expand' + row).innerHTML;
    var all_rows = document.getElementById('schedule_table').rows;
    var button = document.getElementById("toggle_game_gone_button");
    var show = false;
    if (button.value == "Hide started games") show = true;
    for (r = row; r < total_rows; r++) {
        var item = schedule[r];
        if (item.type == HEADER) break;
        game = item.value;
        if (expand == '(-)') all_rows[r].style.display = "none";
        else if (show || !o(game.date, game.time)) all_rows[r].style.display = "";
    }
    if (expand == '(-)') document.getElementById('expand' + row).innerHTML = '(+)';
    else document.getElementById('expand' + row).innerHTML = '(-)';
};

function I(game_number) {
    odds = '';
    in_header = false;
    row = game_lookup[game_number];
    var item = schedule[row].value;
    B(item.date, item.time, row);
    for (t = 0; t < 2; t++) {
        var team = item.team[t];
        var pitcher = item.pitcher[t];
        var hand = item.hand[t];
        var lines = item.lines[t];
        var team_number = item.number + t;
        G(team_number, team, pitcher, hand, '', '');
        for (i = 0; i < number_of_books + 1; i++) {
            f(true, team_number, i, lines[i].value, lines[i].seconds);
        }
    }
    L(item.bgcolor);
    var cells = document.getElementById('schedule_table').rows[row].cells;
    for (i = 0; i < cells.length; i++) {
        cells[i].className = game_row[i].type;
        cells[i].innerHTML = game_row[i].value;
    }
};

function f(full_schedule, team_number, book, value, seconds) {
    index = 't' + team_number + 'b' + book;
    clearTimeout(timers[index]);
    diff = ((new Date()).getTime() / 1000) - clear_time;
    if (seconds > diff) {
        color = '';
    } else if (seconds < 120) {
        color = '1';
        command = 'f (false, ' + team_number + ',' + book + ', "' + value + '",' + 120 + ')';
        interval = (120 - seconds) * 1000;
        timers[index] = setTimeout(command, interval);
    } else if (seconds < 300) {
        color = '2';
        command = 'f (false, ' + team_number + ',' + book + ', "' + value + '",' + 300 + ')';
        interval = (300 - seconds) * 1000;
        timers[index] = setTimeout(command, interval);
    } else if (seconds < 600) {
        color = '3';
        command = 'f (false, ' + team_number + ',' + book + ', "' + value + '",' + 600 + ')';
        interval = (600 - seconds) * 1000;
        timers[index] = setTimeout(command, interval);
    } else {
        color = '';
    }
    game = schedule[game_lookup[team_number]].value;
    game_gone = o(game.date, game.time);
    if (game_gone) color = '9';
    if (full_schedule) P(game.bgcolor, value, color);
    else {
        cell = J(game.bgcolor, color, value, book);
        cell_id = 't' + team_number + 'b' + book;
        if (document.getElementById(cell_id) != null) document.getElementById(cell_id).innerHTML = cell;
    }
};
var changes_timer = null;

function ap() {
    if (changes_timer != null) {
        clearTimeout(changes_timer);
        changes_timer = null;
    }
    v();
};

function v() {
    url = changes_url + '&time=' + changes_time;
    m(0, url, O);
};

function O() {
    if (xml_doc[0] == null) node_value = null;
    else node_value = xml_doc[0].getElementsByTagName("ODDS");
    if (node_value != null && node_value.length > 0) {
        new_schedule_time = node_value[0].getElementsByTagName("SCHEDULE")[0].getAttribute("value");
        if (new_schedule_time != schedule_time) {
            window.location.reload(true);
            return;
        }
        changes_time = node_value[0].getElementsByTagName("TIME")[0].getAttribute("value");
        var leagues = node_value[0].getElementsByTagName("LEAGUE");
        var number_of_leagues = leagues.length;
        for (i = 0; i < number_of_leagues; i++) {
            league_number = leagues[i].getAttribute("number");
            var children = leagues[i].childNodes;
            for (c = 0; c < children.length; c++) {
                var item = children[c];
                if (item.nodeName == 'GAME') {
                    var game_date = item.getAttribute('date');
                    var game_time = item.getAttribute('time');
                    var game_seconds = item.getAttribute('seconds');
                    var number;
                    var team_number;
                    var away_name;
                    var away_pitcher;
                    var away_hand;
                    var home_name;
                    var home_pitcher;
                    var home_hand;
                    var away_score;
                    var home_score;
                    var period = '';
                    var status = '';
                    var teams = item.childNodes;
                    tt = 0;
                    for (t = 0; t < teams.length; t++) {
                        var team = teams[t];
                        if (team.nodeName == 'TEAM') {
                            team_number = parseInt(team.getAttribute('number'));
                            if (tt == 0) {
                                number = team_number;
                                away_name = team.getAttribute('name');
                                away_pitcher = team.getAttribute('pitcher');
                                away_hand = team.getAttribute('hand');
                                away_score = team.getAttribute('score');
                                period = team.getAttribute('status');
                            } else {
                                home_name = team.getAttribute('name');
                                home_pitcher = team.getAttribute('pitcher');
                                home_hand = team.getAttribute('hand');
                                home_score = team.getAttribute('score');
                                status = team.getAttribute('status');
                            }
                            index = game_lookup[team_number];
                            if (index == null) {
                                window.location.reload(true);
                                return;
                            }
                            var line_values = schedule[index].value.lines[tt];
                            var lines = team.childNodes;
                            for (l = 0; l < lines.length; l++) {
                                if (lines[l].nodeName == 'OPENER') {
                                    line_values[0] = new Line(0, lines[l].getAttribute('value'), 9999, "", "");
                                    f(false, team_number, 0, line_values[0].value, 9999);
                                } else if (lines[l].nodeName == 'LINE') {
                                    book_id = parseInt(lines[l].getAttribute('book'));
                                    book = reverse_book[book_id];
                                    seconds = lines[l].getAttribute('seconds');
                                    if (seconds == null || seconds == '') seconds = '9999';
                                    seconds = parseInt(seconds);
									slide = lines[l].getAttribute('slide');
									total = lines[l].getAttribute('total');
                                    line_values[book + 1] = new Line(book_id, lines[l].getAttribute('value'), seconds, slide, total);
                                    f(false, team_number, book + 1, line_values[book + 1].value, seconds);
                                }
                            }
                            tt++;
                        }
                    }
                    var game = schedule[game_lookup[number]].value;
                    if (game_seconds != game.seconds) {
                        H(game, number, game_date, game_time);
                        game.seconds = game_seconds;
                        game.team[0] = away_name;
                        game.team[1] = home_name;
                        I(number);
                    }
                    old_away_score = game.score[0];
                    old_home_score = game.score[1];
                    old_period = game.status[0];
                    old_status = game.status[1];
                    if (away_score != null) {
                        game.score[0] = away_score;
                        game.score[1] = home_score;
                        game.status[0] = period;
                        game.status[1] = status;
                        C(game);
                        ae(number, game.score[0], game.score[1], game.status[0], game.status[1], old_away_score, old_home_score, old_period, old_status);
                    }
                }
            }
        }
    }
    interval = document.getElementById("changes_interval").value;
    changes_timer = setTimeout('v ()', interval * 1000);
    K();
};

function ae(number, away_score, home_score, period, status, old_away_score, old_home_score, old_period, old_status) {
    display_score = '<b>' + away_score + '</b><br><b>' + home_score + '</b>';
    display_status = '' + period + '<br>' + status;
    value = '<table border="0" cellspacing="0" cellpadding="0" width="100%">' + '<tr>' + '<td class="' + changes_2_minutes + '-right" width="33%">' + display_score + '</td>' + '<td class="' + changes_2_minutes + '-left" width="5%">&nbsp;</td>' + '<td class="' + changes_2_minutes + '-left" width="62%">' + display_status + '</td>' + '</tr>' + '</table>';
    document.getElementById('s' + number).innerHTML = value;
};
var lh_count = 0;

function F(book, number, game_date) {
    url = line_history_url + '&book=' + book + '&game=' + number + '&period=' + page_period + '&date=' + game_date.substring(0, 4) + '-' + game_date.substring(4, 6) + '-' + game_date.substring(6);
    winref = window.open(url, 'LH' + (lh_count++), 'width=600,height=400,resizable=1,scrollbars=1;');
};

function K() {
    var currentTime = new Date();
    var hours = currentTime.getHours();
    var minutes = currentTime.getMinutes();
    var seconds = currentTime.getSeconds();
    var ampm = 'am';
    if (hours == 0) hours = '12';
    else if (hours == 12) ampm = 'pm';
    else if (hours > 12) {
        hours -= 12;
        ampm = 'pm'
    }
    if (hours < 10) hours = '0' + hours;
    if (minutes < 10) minutes = '0' + minutes;
    if (seconds < 10) seconds = '0' + seconds;
    display_time = hours + ":" + minutes + ":" + seconds + ampm;
    document.getElementById("last_check").innerHTML = display_time;
};
var game_gone_color = 'game-gone';
var changes_2_minutes = 'redbg';
var changes_5_minutes = 'yellowbg';
var changes_10_minutes = 'greenbg';

function GetCookie(name) {
    var start = document.cookie.indexOf(name + "=");
    var len = start + name.length + 1;
    if ((!start) && (name != document.cookie.substring(0, name.length))) return null;
    if (start == -1) return null;
    var end = document.cookie.indexOf(';', len);
    if (end == -1) end = document.cookie.length;
    return (unescape(document.cookie.substring(len, end)));
};

function k(name, value, days) {
    var today = new Date();
    if (!days) days = 7;
    var expires = days * 60 * 60 * 24 * 1000;
    var expires_date = new Date(today.getTime() + expires);
    var expires_string = expires_date.toGMTString();
    document.cookie = name + '=' + escape(value) + ';path=/' + ';expires=' + expires_string;
};

function ak(name, path, domain) {
    if (GetCookie(name)) document.cookie = name + '=' + ((path) ? ';path=' + path : '') + ((domain) ? ';domain=' + domain : '') + ';expires=Thu, 01-Jan-1970 00:00:01 GMT';
};

function V() {
    var rightNow = new Date();
    var date1 = new Date(rightNow.getFullYear(), 0, 1, 0, 0, 0, 0);
    var date2 = new Date(rightNow.getFullYear(), 6, 1, 0, 0, 0, 0);
    var now_offset = rightNow.getTimezoneOffset();
    var date1_offset = date1.getTimezoneOffset();
    var date2_offset = date2.getTimezoneOffset();
    if (date1_offset == date2_offset) return (0);
    else {
        var current = new Date;
        var start_dst = new Date;
        var end_dst = new Date;
        start_dst.setMonth(2);
        start_dst.setDate(1);
        var day = start_dst.getDay();
        if (day == 0) start_dst.setDate(8);
        else start_dst.setDate(15 - day);
        start_dst.setHours(2);
        start_dst.setMinutes(0);
        start_dst.setSeconds(0);
        end_dst.setMonth(10);
        end_dst.setDate(1);
        end_dst.setHours(2);
        end_dst.setMinutes(0);
        end_dst.setSeconds(0);
        day = end_dst.getDay();
        if (day == 0) end_dst.setDate(1);
        else end_dst.setDate(8 - day);
        if (current >= start_dst && current < end_dst) return (1);
        else
        return (0);
    }
};
var league = 0;
var game_counter = 0;
var betf_counter = 0;
var betf_s_fav = 0;
var betf_s_dog = 0;
var betf_s_tie = 0;
var betf_t_ove = 0;
var betf_t_und = 0;
var betf_t_tie = 0;
var game_color = "";
var game_date = "";
var game_time = "";
var team_counter = 0;
var score = '';
var in_header = false;
var display_line;
var cell_color;
var current_date = '';
var current_time = '';
var odds = '';
var game_number;

function d(data) {
    odds += data;
};

function R(header) {
    if (in_header) {
        display_header += '<br>';
    } else {
        display_header = '';
        in_header = true;
    }
    display_header += header;
};

function aa() {
    if (in_header) d('<tr><td class="title" colspan="' + (7 + number_of_books) + '">' + display_header + '</td></tr>');
};

function B(gd, gt, row) {
    game_date = gd;
    game_time = gt;
    if (in_header) {
        in_header = false;
        game_counter = 0;
		if(betf_counter > 0)
		{
			d('<tr>');
		    d('<td class="headings2" colspan="' + (7 + number_of_books - 6) + '">Bet Summary</td>');
		    d('<td class="headings2" colspan="2">Sides: Fav-' + betf_s_fav +' Dog-'+ betf_s_dog +' Push-'+ betf_s_tie + '</td>');
		    d('<td class="headings2" colspan="2">O/U: Over-' + betf_t_ove +' Under-'+ betf_t_und +' Tie-'+ betf_t_tie + '</td>');
		    d('</tr>');
			betf_counter = 0;
			betf_s_fav = 0; betf_s_dog = 0; betf_s_tie = 0;
			betf_t_ove = 0; betf_t_und = 0; betf_t_tie = 0;
		}
        d('<tr><td class="title" colspan="' + (7 + number_of_books) + '">' + display_header + '</td></tr>');
        d('<tr>');
        d('<td class="headings" width="50">Time</td>');
        d('<td class="headings" width="50">Team#</td>');
        d('<td class="headings" width="250" style="text-align:left;margin-left:20px;">Team Name</td>');
        d('<td class="headings" width="90">Opener</td>');
        for (i = 0; i < number_of_books-1; i++) {
            d('<td class="headings" width="90">');
            if (books[i].link.length == 0) d(books[i].name + '</td>');
            else d('' + books[i].name + '</td>');
        }
        d('<td class="headings" width="100">Score</td>');
		for (i = number_of_books-1; i < number_of_books; i++) {
            d('<td class="headings" width="90">');
            if (books[i].link.length == 0) d(books[i].name + '</td>');
            else d('' + books[i].name + '</td>');
        }
        d('</tr>');
    }
    game_color = "white";
    display_number = '';
    display_team = '';
    display_score = '';
    display_status = '';
    for (i = 0; i < number_of_books + 1; i++) display_line[i] = '';
};

function o(game_date, game_time) {
    ai();
    if (game_date < current_date || (game_date == current_date && game_time <= current_time)) {
        return (true);
    } else {
        return (false);
    }
};

function g(type, value) {
    this.type = type;
    this.value = value;
};
var game_row = new Object();

function L(bgcolor) {
    game_gone = o(game_date, game_time);
    if (game_gone) display_class = game_gone_color;
    if (!game_gone || display_class == 'whitebg') {
        if (bgcolor == 0) display_class = 'whitebg';
        else display_class = 'greybg';
    }
    d('<tr>');
    var game_col = new g(display_class, game_date.substring(4, 6) + '/' + game_date.substring(6) + '<br>' + U(game_time));
    game_row[0] = game_col;
    d('<td class="' + game_col.type + '">' + game_col.value + '</td>');
    game_col = new g(display_class, display_number);
    game_row[1] = game_col;
    //d('<td style="cursor:pointer" class="' + game_col.type + '" onClick="F(1,' + game_number + ',\'' + game_date + '\')">' + game_col.value + '</td>');
	d('<td class="' + game_col.type + '">' + game_col.value + '</td>');
    if (baseball) {
        value = '<table border="0" cellspacing="0" cellpadding="0" width="100%">' + '<tr><td class="' + display_class + '-left" width="25%">' + display_team + '</td>' + '<td width="5%">&nbsp;</td>' + '<td class="' + display_class + '-left" width="70%">' + display_pitcher + '</td>' + '</tr>' + '</table>';
        game_col = new g(display_class + '-left', value);
        game_row[2] = game_col;
        d('<td class="' + game_col.type + '" width="200">' + game_col.value + '</td>');
    } else {
        game_col = new g(display_class + '-left', display_team);
        game_row[2] = game_col;
        d('<td class="' + game_col.type + '">' + game_col.value + '</td>');
    }
    value = '<table border="0" cellspacing="0" cellpadding="0" width="100%">' + '<tr>' + '<td class="' + display_class + '-right" width="33%">' + display_score + '</td>' + '<td width="5%">&nbsp;</td>' + '<td class="' + display_class + '-left" width="62%">' + display_status + '</td>' + '</tr>' + '</table>';
    
    game_col = new g(display_class + '-right', display_line[0]);
    game_row[4] = game_col;
    d('<td class="' + game_col.type + '">' + game_col.value + '</td>');
	var lg_sport = document.getElementById("league_sport").value;
	display_team = display_team.replace(/\xa0|(\&nbsp;)/g, ' ');
    for (i = 0; i < number_of_books-1; i++) {
        game_col = new g(display_class + '-right', display_line[i + 1]);
        game_row[5 + i] = game_col;
		d('<td style="cursor:pointer" class="' + game_col.type + '" onClick="F(' + books[i].id + ',' + game_number + ',\'' + game_date + '\')">' + game_col.value + '</td>');
    }
	game_col = new g(display_class + '-left', value);
    game_row[3] = game_col;
	
    d('<td id="s' + game_number + '" class="' + game_col.type + '">' + game_col.value + '</td>');//final score
	
	//bet final
	for (i = number_of_books-1; i < number_of_books; i++) {
        game_col = new g(display_class + '-right', display_line[i + 1]);
        game_row[5 + i] = game_col;
		d('<td class="' + game_col.type + '">' + game_col.value + '</td>');
		if(game.lines[0][number_of_books].slide != '' || game.lines[0][number_of_books].total != '')
		{
			var sl = game.lines[0][number_of_books].slide;
			var tl = game.lines[0][number_of_books].total;
			switch( sl )
			{
				case "Favorite": 
					betf_s_fav++;
					break;
				case "Dog": 
					betf_s_dog++;
					break;
				case "Push": 
					betf_s_tie++;
					break;
			}
			switch( tl )
			{
				case "Over": 
					betf_t_ove++;
					break;
				case "Under": 
					betf_t_und++;
					break;
				case "Tie": 
					betf_t_tie++;
					break;
			}
			betf_counter++;
		}
    }
	//bet final
	
    d('</tr>');
	row = game_lookup[game_number];
	if(row == total_rows-1 && betf_counter > 0)
	{
	  d('<tr>');
	  d('<td class="headings2" colspan="' + (7 + number_of_books - 6) + '">Bet Summary</td>');
	  d('<td class="headings2" colspan="2">Sides: Fav-' + betf_s_fav +' Dog-'+ betf_s_dog +' Push-'+ betf_s_tie + '</td>');
	  d('<td class="headings2" colspan="2">O/U: Over-' + betf_t_ove +' Under-'+ betf_t_und +' Tie-'+ betf_t_tie + '</td>');
	  d('</tr>');
	}
};

function G(number, team, pitcher, hand, score, status) {
    if (display_number.length == 0) game_number = number;
    else display_number += '<br>';
    display_number += number;
    if (pitcher != null && pitcher.length > 0) {
        baseball = true;
        b = pitcher.indexOf(' ');
        if (b > 0) {
            b2 = pitcher.substring(b + 1).indexOf(' ');
            if (b2 >= 0) b += b2 + 1;
            pitcher = pitcher.substring(0, b) + ' ' + pitcher.substring(b + 1, b + 2) + pitcher.substring(b + 2).toLowerCase();
        } else pitcher = pitcher.substring(0, 1) + pitcher.substring(1).toLowerCase();
        if (display_team.length == 0) {
            if (pitcher.length > 0) display_pitcher = pitcher;
            else if (team.length > 3 && team.charAt(3) == ' ') display_pitcher = team.substring(4);
            else if (team.length > 3 && team.charAt(3) != ' ') display_pitcher = team;
            else display_pitcher = '';
            if (hand == 'L') display_pitcher += '<b>-' + hand + '</b>';
            display_pitcher += '<br>';
            display_team = '&nbsp;' + team.substring(0, 3) + '<br>';
        } else {
            if (pitcher.length > 0) display_pitcher += pitcher;
            else if (team.length > 3 && team.charAt(3) == ' ') display_pitcher += team.substring(4);
            else if (team.length > 3 && team.charAt(3) != ' ') display_pitcher += team;
            if (hand == 'L') display_pitcher += '<b>-' + hand + '</b>';
            display_team += '&nbsp;' + team.substring(0, 3);
        }
    } else {
        baseball = false;
        if (display_team.length == 0) display_team = '&nbsp;' + team + '<br>';
        else display_team += '&nbsp;' + team;
        display_pitcher = '';
    }
    if (score == '255' || score.length == 0) score = '';
    if (score.length > 0 && Q(score.substring(0, 1))) bold_score = '<b>' + score + '</b>';
    else bold_score = score;
    if (display_score.length == 0) display_score = bold_score + '<br>';
    else display_score += bold_score;
    if (display_status.length == 0) display_status = status + '<br>';
    else display_status += status;
    book = 0;
};

function P(bgcolor, value, color) {
    var home = true;
    if (display_line[book].length == 0) {
        home = false;
        id = ' id="t' + game_number + 'b' + book + '"';
        display_line[book] = '<table width="100%" cellspacing="0" cellpadding="0"><tr><td' + id + '>';
    } else {
        id = ' id="t' + (game_number + 1) + 'b' + book + '"';
        display_line[book] += '</td></tr><tr><td' + id + '>';
    }
    display_line[book] += J(bgcolor, color, value, book);
    if (home) display_line[book] += '</td></tr></table>';
    book++;
};

function J(bgcolor, color, value, bookCounter) {
    if (value.length == 0 || value == ' ') cell = '&nbsp;';
    else cell = value;
	if(bookCounter > 0)cell = '<b>' + cell + '</b>';
    cell += '&nbsp;&nbsp;';
    if (color == '1') return '<table width="100%" cellspacing="0" cellpadding="0"><tr><td class="' + changes_2_minutes + '">' + cell + '</td></tr></table>';
    else if (color == '2') return '<table width="100%" cellspacing="0" cellpadding="0"><tr><td class="' + changes_5_minutes + '">' + cell + '</td></tr></table>';
    else if (color == '3') return '<table width="100%" cellspacing="0" cellpadding="0"><tr><td class="' + changes_10_minutes + '">' + cell + '</td></tr></table>';
    else if (color == '9' && game_gone_color != 'whitebg') return '<table width="100%" cellspacing="0" cellpadding="0"><tr><td class="' + game_gone_color + '">' + cell + '</td></tr></table>';
    else if (bgcolor == 0) return '<table width="100%" cellspacing="0" cellpadding="0"><tr><td class="whitebg">' + cell + '</td></tr></table>';
    else
    return '<table width="100%" cellspacing="0" cellpadding="0"><tr><td class="greybg">' + cell + '</td></tr></table>';
};

function U(ctime) {
    minutes = ctime.substring(2);
    hour = ctime.substring(0, 2);
    if (hour.charAt(0) == '0') game_hour = parseInt(hour.substring(1));
    else game_hour = parseInt(hour);
    ampm = 'a';
    if (game_hour >= 12) {
        if (game_hour >= 13) game_hour -= 12;
        ampm = 'p';
    }
    value = game_hour + minutes + ampm;
    if (game_hour >= 10) return (value.substring(0, 2) + ':' + value.substring(2));
    else if (game_hour == 0) return ('12:' + value.substring(1));
    else
    return (value.substring(0, 1) + ':' + value.substring(1));
};

function Q(num) {
    if (num.length > 1) return false;
    var string = "1234567890";
    if (string.indexOf(num) != -1) return true;
    return false;
};

function ai() {
    var now = new Date();
    var year = now.getFullYear();
    var month = now.getMonth() + 1;
    var date = now.getDate();
    current_date = ('' + year) + (month < 10 ? '0' + month : '' + month) + (date < 10 ? '0' + date : '' + date);
    var hours = now.getHours();
    var minutes = now.getMinutes();
    var seconds = now.getSeconds();
    current_time = (hours < 10 ? '0' + hours : '' + hours) + (minutes < 10 ? '0' + minutes : '' + minutes);
}

function change_fclass(av,ty) {

	//alert('av:' + av);
	if(ty=='on')
	{
	  	//av.style.background = "-moz-linear-gradient(center top , #CC0001 0%, #660000 100%) repeat scroll 0 0 transparent";
	}else
	{
		//av.style.background = "none";
	}
}