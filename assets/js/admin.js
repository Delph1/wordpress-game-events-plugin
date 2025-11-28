/**
 * Admin JavaScript
 */

(function ($) {
    $(document).ready(function () {
        // Season Management
        $("#hge-season-form").on("submit", function (e) {
            e.preventDefault();
            saveSeason();
        });

        $("#hge-season-reset").on("click", function () {
            resetSeasonForm();
        });

        $(".hge-edit-season").on("click", function () {
            editSeason($(this).data("season-id"));
        });

        $(".hge-delete-season").on("click", function () {
            deleteSeason($(this).data("season-id"), $(this).closest("tr"));
        });

        // Team Management
        $("#hge-team-form").on("submit", function (e) {
            e.preventDefault();
            saveTeam();
        });

        $("#hge-team-reset").on("click", function () {
            resetTeamForm();
        });

        $(".hge-edit-team").on("click", function () {
            editTeam($(this).data("team-id"));
        });

        $(".hge-delete-team").on("click", function () {
            deleteTeam($(this).data("team-id"), $(this).closest("tr"));
        });

        // Player Management
        $("#hge-player-form").on("submit", function (e) {
            e.preventDefault();
            savePlayer();
        });

        $("#hge-player-reset").on("click", function () {
            resetPlayerForm();
        });

        $(".hge-edit-player").on("click", function () {
            editPlayer($(this).data("player-id"));
        });

        $(".hge-delete-player").on("click", function () {
            deletePlayer($(this).data("player-id"), $(this).closest("tr"));
        });

        // Game Management
        $("#hge-game-form").on("submit", function (e) {
            e.preventDefault();
            saveGame();
        });

        $("#hge-game-reset").on("click", function () {
            resetGameForm();
        });

        $(".hge-edit-game").on("click", function () {
            editGame($(this).data("game-id"));
        });

        $(".hge-delete-game").on("click", function () {
            deleteGame($(this).data("game-id"), $(this).closest("tr"));
        });

        $(".hge-manage-events").on("click", function () {
            openEventsModal($(this).data("game-id"));
        });

        // Event Management
        $("#hge-event-form").on("submit", function (e) {
            e.preventDefault();
            saveEvent();
        });

        $("#hge-event-reset").on("click", function () {
            resetEventForm();
        });

        // Modal close
        $(".hge-close").on("click", function () {
            closeEventsModal();
        });

        $(window).on("click", function (e) {
            const modal = $("#hge-events-modal")[0];
            if (e.target == modal) {
                closeEventsModal();
            }
        });
    });

    // Season Functions
    function saveSeason() {
        const form = $("#hge-season-form");
        const data = form.serialize() + "&action=hge_save_season&nonce=" + hgeAdmin.nonce;

        $.post(hgeAdmin.ajaxUrl, data, function (response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.data || "Error saving season");
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error:", textStatus, errorThrown);
            alert("Error saving season: " + textStatus);
        });
    }

    function editSeason(seasonId) {
        $.post(hgeAdmin.ajaxUrl, { action: "hge_get_season", id: seasonId, nonce: hgeAdmin.nonce }, function (response) {
            if (response.success) {
                const season = response.data;
                $("#hge-season-id").val(season.id);
                $("#hge-season-name").val(season.name);
                $("#hge-season-description").val(season.description);
                $("html, body").animate({ scrollTop: 0 }, "fast");
            }
        });
    }

    function deleteSeason(seasonId, row) {
        if (confirm("Are you sure you want to delete this season?")) {
            $.post(hgeAdmin.ajaxUrl, { action: "hge_delete_season", id: seasonId, nonce: hgeAdmin.nonce }, function (response) {
                if (response.success) {
                    row.remove();
                } else {
                    alert("Error deleting season");
                }
            });
        }
    }

    function resetSeasonForm() {
        $("#hge-season-form")[0].reset();
        $("#hge-season-id").val("0");
    }

    // Team Functions
    function saveTeam() {
        const form = $("#hge-team-form");
        const data = form.serialize() + "&action=hge_save_team&nonce=" + hgeAdmin.nonce;

        $.ajax({
            type: "POST",
            url: hgeAdmin.ajax_url,
            data: data,
            success: function (response) {
                if (response.success) {
                    alert(hgeAdmin.strings.saved);
                    resetTeamForm();
                    location.reload();
                } else {
                    alert(response.data || hgeAdmin.strings.error);
                }
            },
            error: function () {
                alert(hgeAdmin.strings.error);
            },
        });
    }

    function editTeam(teamId) {
        $.ajax({
            type: "GET",
            url: hgeAdmin.ajax_url + "?action=hge_get_team&id=" + teamId,
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    const team = response.data;
                    $("#hge-team-id").val(team.id);
                    $("#hge-team-name").val(team.name);
                    $("#hge-team-shortcode").val(team.shortcode);
                    $("#hge-team-form-container").scrollIntoView();
                } else {
                    alert(hgeAdmin.strings.error);
                }
            },
        });
    }

    function deleteTeam(teamId, row) {
        if (confirm(hgeAdmin.strings.confirm_delete)) {
            $.ajax({
                type: "POST",
                url: hgeAdmin.ajax_url,
                data: {
                    action: "hge_delete_team",
                    id: teamId,
                    nonce: hgeAdmin.nonce,
                },
                success: function (response) {
                    if (response.success) {
                        row.fadeOut(300, function () {
                            $(this).remove();
                        });
                    } else {
                        alert(hgeAdmin.strings.error);
                    }
                },
            });
        }
    }

    function resetTeamForm() {
        $("#hge-team-form")[0].reset();
        $("#hge-team-id").val(0);
    }

    // Player Functions
    function savePlayer() {
        const form = $("#hge-player-form");
        const data = form.serialize() + "&action=hge_save_player&nonce=" + hgeAdmin.nonce;

        $.ajax({
            type: "POST",
            url: hgeAdmin.ajax_url,
            data: data,
            success: function (response) {
                if (response.success) {
                    alert(hgeAdmin.strings.saved);
                    resetPlayerForm();
                    location.reload();
                } else {
                    alert(response.data || hgeAdmin.strings.error);
                }
            },
            error: function () {
                alert(hgeAdmin.strings.error);
            },
        });
    }

    function editPlayer(playerId) {
        $.ajax({
            type: "GET",
            url: hgeAdmin.ajax_url + "?action=hge_get_player&id=" + playerId,
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    const player = response.data;
                    $("#hge-player-id").val(player.id);
                    $("#hge-player-name").val(player.name);
                    $("#hge-player-number").val(player.number);
                    $("#hge-player-position").val(player.position);
                    $("#hge-player-goalie").prop("checked", player.is_goalie == 1);
                    $("#hge-player-form-container").scrollIntoView();
                } else {
                    alert(hgeAdmin.strings.error);
                }
            },
        });
    }

    function deletePlayer(playerId, row) {
        if (confirm(hgeAdmin.strings.confirm_delete)) {
            $.ajax({
                type: "POST",
                url: hgeAdmin.ajax_url,
                data: {
                    action: "hge_delete_player",
                    id: playerId,
                    nonce: hgeAdmin.nonce,
                },
                success: function (response) {
                    if (response.success) {
                        row.fadeOut(300, function () {
                            $(this).remove();
                        });
                    } else {
                        alert(hgeAdmin.strings.error);
                    }
                },
            });
        }
    }

    function resetPlayerForm() {
        $("#hge-player-form")[0].reset();
        $("#hge-player-id").val(0);
    }

    // Game Functions
    function saveGame() {
        const form = $("#hge-game-form");
        const data = form.serialize() + "&action=hge_save_game&nonce=" + hgeAdmin.nonce;

        $.ajax({
            type: "POST",
            url: hgeAdmin.ajax_url,
            data: data,
            success: function (response) {
                if (response.success) {
                    alert(hgeAdmin.strings.saved);
                    resetGameForm();
                    location.reload();
                } else {
                    alert(response.data || hgeAdmin.strings.error);
                }
            },
            error: function () {
                alert(hgeAdmin.strings.error);
            },
        });
    }

    function editGame(gameId) {
        $.ajax({
            type: "GET",
            url: hgeAdmin.ajax_url + "?action=hge_get_game&id=" + gameId,
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    const game = response.data;
                    $("#hge-game-id").val(game.id);
                    $("#hge-game-date").val(game.game_date);
                    $("#hge-game-opponent").val(game.opponent);
                    $("#hge-game-location").val(game.location);
                    $("#hge-game-home-score").val(game.home_score);
                    $("#hge-game-away-score").val(game.away_score);
                    $("#hge-game-notes").val(game.notes);
                    $("#hge-game-form-container").scrollIntoView();
                } else {
                    alert(hgeAdmin.strings.error);
                }
            },
        });
    }

    function deleteGame(gameId, row) {
        if (confirm(hgeAdmin.strings.confirm_delete)) {
            $.ajax({
                type: "POST",
                url: hgeAdmin.ajax_url,
                data: {
                    action: "hge_delete_game",
                    id: gameId,
                    nonce: hgeAdmin.nonce,
                },
                success: function (response) {
                    if (response.success) {
                        row.fadeOut(300, function () {
                            $(this).remove();
                        });
                    } else {
                        alert(hgeAdmin.strings.error);
                    }
                },
            });
        }
    }

    function resetGameForm() {
        $("#hge-game-form")[0].reset();
        $("#hge-game-id").val(0);
    }

    // Event Functions
    function openEventsModal(gameId) {
        $("#hge-event-game-id").val(gameId);
        $("#hge-events-modal").show();
        loadGameEvents(gameId);
        loadGameDetails(gameId);
    }

    function closeEventsModal() {
        $("#hge-events-modal").hide();
        resetEventForm();
    }

    function loadGameDetails(gameId) {
        $.ajax({
            type: "GET",
            url: hgeAdmin.ajax_url + "?action=hge_get_game&id=" + gameId,
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    const game = response.data;
                    const title = game.game_date + " vs " + game.opponent;
                    $("#hge-events-modal-title").text(title);
                }
            },
        });
    }

    function loadGameEvents(gameId) {
        $.ajax({
            type: "GET",
            url: hgeAdmin.ajax_url + "?action=hge_get_game_events&id=" + gameId,
            dataType: "json",
            success: function (response) {
                if (response.success && response.data) {
                    const events = response.data;
                    let html = "<ul>";
                    events.forEach(function (event) {
                        html +=
                            "<li>";
                        html +=
                            "P" +
                            event.period +
                            " " +
                            event.event_time +
                            ":00 - " +
                            event.event_type;
                        if (event.name) {
                            html += " (" + event.name + ")";
                        }
                        html +=
                            ' <button class="button button-link-delete hge-delete-single-event" data-event-id="' +
                            event.id +
                            '">Delete</button>';
                        html += "</li>";
                    });
                    html += "</ul>";
                    $("#hge-events-list").html(html);

                    $(".hge-delete-single-event").on("click", function () {
                        deleteSingleEvent($(this).data("event-id"), gameId);
                    });
                } else {
                    $("#hge-events-list").html(
                        "<p>No events yet.</p>"
                    );
                }
            },
        });
    }

    function saveEvent() {
        const form = $("#hge-event-form");
        const data = form.serialize() + "&action=hge_save_event&nonce=" + hgeAdmin.nonce;
        const gameId = $("#hge-event-game-id").val();

        $.ajax({
            type: "POST",
            url: hgeAdmin.ajax_url,
            data: data,
            success: function (response) {
                if (response.success) {
                    alert(hgeAdmin.strings.saved);
                    resetEventForm();
                    loadGameEvents(gameId);
                } else {
                    alert(response.data || hgeAdmin.strings.error);
                }
            },
            error: function () {
                alert(hgeAdmin.strings.error);
            },
        });
    }

    function deleteSingleEvent(eventId, gameId) {
        if (confirm(hgeAdmin.strings.confirm_delete)) {
            $.ajax({
                type: "POST",
                url: hgeAdmin.ajax_url,
                data: {
                    action: "hge_delete_event",
                    id: eventId,
                    nonce: hgeAdmin.nonce,
                },
                success: function (response) {
                    if (response.success) {
                        loadGameEvents(gameId);
                    } else {
                        alert(hgeAdmin.strings.error);
                    }
                },
            });
        }
    }

    function resetEventForm() {
        $("#hge-event-form")[0].reset();
        $("#hge-event-id").val(0);
    }
})(jQuery);
