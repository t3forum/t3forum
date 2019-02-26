jQuery(document).ready(function($) {
	// forum_last_post_summary
	var postSummarys  = [];
	var postSummarysCount  = 0;

    $('body').on('click', '[data-toggle]', function (e) {
        var $target = $(this).data('toggle');
        $('#'  + $target).toggle();
        e.preventDefault();
    });

	$('.post_summary_box').each(function (index) {
		postSummarys[postSummarysCount] = {};
		postSummarys[postSummarysCount]['type'] = $(this).data('type');
		postSummarys[postSummarysCount]['hiddenimage'] = $(this).data('hiddenimage');
		postSummarys[postSummarysCount]['uid'] = $(this).data('uid');
		postSummarysCount = postSummarysCount + 1;
	});

	// onlinepoint
	var displayedUser = [];
	var displayedUserCount = 0;
	$('.user_onlinepoint').each(function (index) {
		displayedUser[displayedUserCount] = $(this).data('uid');
		displayedUserCount = displayedUserCount + 1;
	});

	// topicIcons
	var displayedTopicIcons = [];
	var displayedTopicIconsCount = 0;
	$('.topic_icon').each(function (index) {
		displayedTopicIcons[displayedTopicIconsCount] = $(this).data('uid');
		displayedTopicIconsCount = displayedTopicIconsCount + 1;
	});

	// forumIcons
	var displayedForumIcons = [];
	var displayedForumIconsCount = 0;
	$('.forum_icon').each(function (index) {
		displayedForumIcons[displayedForumIconsCount] = $(this).data('uid');
		displayedForumIconsCount = displayedForumIconsCount + 1;
	});

	// forumMenus
	var displayedForumMenus = [];
	var displayedForumMenusCount = 0;
	$('.forum_menu').each(function (index) {
		displayedForumMenus[displayedForumMenusCount] = $(this).data('uid');
		displayedForumMenusCount = displayedForumMenusCount + 1;
	});

	// topicIcons
	var displayedTopics = [];
	var displayedTopicsCount = 0;
	$('.topic_entry').each(function (index) {
		displayedTopics[displayedTopicsCount] = $(this).data('uid');
		displayedTopicsCount = displayedTopicsCount + 1;
	});

	var displayOnlinebox = 0;
	if ($('.user_online_box').length > 0) {
		displayOnlinebox = 1;
	}

	// post_item
	var displayedPosts = [];
	var displayedPostCount = 0;
	$('.post_item').each(function (index) {
		displayedPosts[displayedPostCount] = $(this).data('uid');
		displayedPostCount = displayedPostCount + 1;
	});

	// ads
	var displayedAds = {};
	var displayedAdsIteration = 0;
	$('.ad-topic').each(function (index) {
		displayedAdsIteration = displayedAdsIteration + 1;
	});
	$('.ad-topicdetail').each(function (index) {
		displayedAdsIteration = displayedAdsIteration + 1;
	});
	displayedAds['count'] = displayedAdsIteration;
	displayedAds['mode'] = 0;
	$('.userInfo').each(function (index) {
		displayedAds['mode'] = 1;

	});
	/* 
		support old layout versions
	*/
	if (typeof t3forum_ajaxUrl === 'undefined'
		&& typeof currentPageUid !== 'undefined'
	) {
        t3forum_ajaxUrl = "?id=" + currentPageUid + "&eID=t3forum&language=de&tx_t3forum_ajax[controller]=Ajax&tx_t3forum_ajax[action]=main&tx_t3forum_ajax[format]=json";
    }
    if (typeof t3forum_ajaxUrl_helpful === 'undefined'
    	&& typeof currentPageUid !== 'undefined'
    ) {
        t3forum_ajaxUrl_helpful = "index.php?id=" + currentPageUid +
            "&eID=" + "__t3forum_eid__" +
            "&tx_t3forum_ajax[controller]=Post" +
            "&tx_t3forum_ajax[action]=" + "__t3forum_action__" +
            "&tx_t3forum_ajax[post]=" + "__t3forum_post__";
    }
    function isValidJSON(text) {
        try {
            var json = JSON.parse(text);
            return json;
        } catch {
            return false;
        }
    }
    if (typeof t3forum_ajaxUrl !== 'undefined') {
        $.ajax({
            type: "POST",
            url: t3forum_ajaxUrl,
            async: true,
            data: {
                "tx_t3forum_ajax[displayedUser]": JSON.stringify(displayedUser),
                "tx_t3forum_ajax[postSummarys]": JSON.stringify(postSummarys),
                "tx_t3forum_ajax[topicIcons]": JSON.stringify(displayedTopicIcons),
                "tx_t3forum_ajax[forumIcons]": JSON.stringify(displayedForumIcons),
                "tx_t3forum_ajax[displayedTopics]": JSON.stringify(displayedTopics),
                "tx_t3forum_ajax[displayOnlinebox]": JSON.stringify(displayOnlinebox),
                "tx_t3forum_ajax[displayedForumMenus]": JSON.stringify(displayedForumMenus),
                "tx_t3forum_ajax[displayedPosts]": JSON.stringify(displayedPosts),
                "tx_t3forum_ajax[displayedAds]": JSON.stringify(displayedAds)
            },
            success: function (data) {
                if (!data) {
                    $('.forum_menu[data-uid="' + $(this).data('uid') + '"]').html('<div>AJAX FEHLER: Keine Daten erhalten!</div>');
                    return false;
                }
                json = isValidJSON(data);
                if (!json) {
                    if ($('.forum_menu')) {
                        $('.forum_menu').each(function (index) {
                            if ($('.forum_menu[data-uid="' + $(this).data('uid') + '"]')) {
                                console.log(data);
                                $('.forum_menu[data-uid="' + $(this).data('uid') + '"]').html('<div>AJAX FEHLER: Kein gültiges JSON erhalten!</div>');
                            }
                        });
                    }
                    return false;
                }
                if (json.topicIcons) {
                    json.topicIcons.forEach(function (entry) {
                        $('.topic_icon[data-uid="' + entry.uid + '"]').html(entry.html);
                    });
                }
                if (json.forumMenus) {
                    json.forumMenus.forEach(function (entry) {
                        $('.forum_menu[data-uid="' + entry.uid + '"]').html(entry.html);
                    });
                }
                if (json.forumIcons) {
                    json.forumIcons.forEach(function (entry) {
                        $('.forum_icon[data-uid="' + entry.uid + '"]').html(entry.html);
                    });
                }
                if (json.postSummarys) {
                    json.postSummarys.forEach(function (entry) {
                        $('.post_summary_box[data-uid="' + entry.uid + '"][data-type="' + entry.type + '"]').html(entry.html);
                    });
                }
                if (json.topics) {
                    json.topics.forEach(function (entry) {
                        $('.topic_reply_count[data-uid="' + entry.uid + '"]').html(entry.replyCount);
                        $('.topic_list_menu[data-uid="' + entry.uid + '"]').html(entry.topicListMenu);
                    });
                }
                if (json.posts) {
                    json.posts.forEach(function (entry) {
                        $('.postHelpfulCount_' + entry.uid + '').html(entry.postHelpfulCount);
                        $('.postUserHelpfulCount_' + entry.author.uid + '').html(entry.postUserHelpfulCount);
                        $('.post_helpful_button[data-uid="' + entry.uid + '"]').html(entry.postHelpfulButton);
                        $('.post_edit_link[data-uid="' + entry.uid + '"]').html(entry.postEditLink);
                    });
                }
                if (json.onlineUser) {
                    json.onlineUser.forEach(function (entry) {
                        $('.user_onlinepoint[data-uid="' + entry + '"]').removeClass('iconset-14-user-offline');
                        $('.user_onlinepoint[data-uid="' + entry + '"]').addClass('iconset-14-user-online');
                    });
                }
                if (json.onlineBox) {
                    $('.user_online_box .items').html(json.onlineBox.html);
                    $('.user_online_count').html(json.onlineBox.count);
                }

                if (json.ads) {
                    if ($('#infomation-' + json.ads.position).length > 0) {
                        $('#infomation-' + json.ads.position).html(json.ads.html).show();
                    }
                }

                $('.tx-t3forum-helpfull-btn').click(function () {
                    var targetElement = this;
                    var counttargetVal = $('.' + $(targetElement).attr('data-counttarget')).html();
                    var countusertargetVal = $('.' + $(targetElement).attr('data-countusertarget')).html();
                    var type = 'add';
                    var action = 'addSupporter';
                    var eID  = $(this).attr('data-eid');
                    var post = $(this).attr('data-post');
                    if ($(targetElement).hasClass('supported')) {
                        type = 'remove';
                        action = 'removeSupporter';
                    }
                    $.ajax({
                        type: "GET",
                        url: "index.php?id=" + currentPageUid + 
                              "&eID=" + $(this).attr('data-eid') + 
                              "&tx_t3forum_ajax[controller]=Post" + 
                              "&tx_t3forum_ajax[action]=" + type + "Supporter" + 
                              "&tx_t3forum_ajax[post]=" + $(this).attr('data-post') + 
                              '&no_cache=1',
                              /*
                              t3forum_ajaxUrl_helpful
                                .replace('__t3forum_eid__', eID)
                                .replace('__t3forum_action__', action)
                                .replace('__t3forum_post__',post),
                              */
                        async: false,
                        beforeSend: function (msg) {
                            $('.' + $(targetElement).attr('data-counttarget')).html('<div class="tx-t3forum-ajax-loader"></div>');
                            $('.' + $(targetElement).attr('data-countusertarget')).html('<div class="tx-t3forum-ajax-loader"></div>');
                        },
                        success: function (data) {
                            var json = $.parseJSON(data);
                            if (json.error) {
                                $('.' + $(targetElement).attr('data-counttarget')).html(counttargetVal);
                                $('.' + $(targetElement).attr('data-countusertarget')).html(countusertargetVal);
                                return
                            }
                            if (type == 'add') {
                                $(targetElement).addClass('supported');
                            } else {
                                if ($(targetElement).hasClass('supported')) {
                                    $(targetElement).removeClass('supported');
                                }
                            }
                            $('.' + $(targetElement).attr('data-counttarget')).html(json.postHelpfulCount);
                            $('.' + $(targetElement).attr('data-countusertarget')).html(json.userHelpfulCount);
                        }
                    });
                });
            }
        });
    }

});
