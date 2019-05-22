if (!BXRS)
{
	var BXRS = {};
}

Rating = function(voteId, entityTypeId, entityId, available, userId, localize, pathToUserProfile)
{	
	this.enabled = true;
	this.voteId = voteId;
	this.entityTypeId = entityTypeId;
	this.entityId = entityId;
	this.available = available == 'Y'? true: false;
	this.userId = userId;
	this.localize = localize;	
	this.pathToUserProfile = pathToUserProfile;
	
	this.box = BX('rating-vote-'+voteId);
	if (this.box === null)
	{
		this.enabled = false;
		return false;
	}
	this.result = BX('rating-vote-'+voteId+'-result');
	this.buttonPlus = BX('rating-vote-'+voteId+'-plus');
	this.buttonMinus = BX('rating-vote-'+voteId+'-minus');
	this.voteProcess = false;
}

Rating.Set = function(voteId, entityTypeId, entityId, available, userId, localize, pathToUserProfile)
{
	BXRS[voteId] = new Rating(voteId, entityTypeId, entityId, available, userId, localize, pathToUserProfile);
	if (BXRS[voteId].enabled)
		Rating.Init(voteId);	
};

Rating.Init = function(voteId)
{
	if (BXRS[voteId].available)
	{
		BX.bind(BXRS[voteId].buttonPlus, 'click' , function()	{
			if (BXRS[voteId].voteProcess)
				return false;
				
			BXRS[voteId].voteProcess = true;	
			BX.addClass(this, 'rating-vote-load');
			if (BX.hasClass(this, 'rating-vote-plus-active'))
			{
				Rating.Vote(voteId, 'plus', 'cancel');
			}
			else
			{
				Rating.Vote(voteId, 'plus', 'plus');
			}
			return false;
		});
		
		BX.bind(BXRS[voteId].buttonMinus, 'click' , function()	{
			if (BXRS[voteId].voteProcess)
				return false;
			
			BXRS[voteId].voteProcess = true;
			BX.addClass(this, 'rating-vote-load');
			if (BX.hasClass(this, 'rating-vote-minus-active'))
			{
				Rating.Vote(voteId, 'minus', 'cancel');
			}
			else
			{
				Rating.Vote(voteId, 'minus', 'minus');
			}
			return false;
		});
	}
}

Rating.UpdateStatus = function(voteId, button, action)
{
	BXRS[voteId].buttonPlus.title = (action == 'cancel' || button == 'minus' ? BXRS[voteId].localize['PLUS']: BXRS[voteId].localize['CANCEL']); 
	BXRS[voteId].buttonMinus.title = (action == 'cancel' || button == 'plus' ? BXRS[voteId].localize['MINUS']: BXRS[voteId].localize['CANCEL']); 				
	BX.removeClass(BXRS[voteId].buttonPlus, (button == 'plus'? 'rating-vote-load': 'rating-vote-plus-active'));
	BX.removeClass(BXRS[voteId].buttonMinus, (button == 'plus'? 'rating-vote-minus-active': 'rating-vote-load'));	
	if (action == 'cancel')
		BX.removeClass(button == 'plus'? BXRS[voteId].buttonPlus: BXRS[voteId].buttonMinus, 'rating-vote-'+button+'-active');
	else
		BX.addClass(button == 'plus'? BXRS[voteId].buttonPlus: BXRS[voteId].buttonMinus, 'rating-vote-'+button+'-active');
}

Rating.Vote = function(voteId, button, action)
{
	BX.ajax({
		url: '/bitrix/components/bitrix/rating.vote/vote.ajax.php',
		method: 'POST',
		dataType: 'json',
		data: {'RATING_VOTE' : 'Y', 'RATING_RESULT' : 'Y', 'RATING_VOTE_TYPE_ID' : BXRS[voteId].entityTypeId, 'RATING_VOTE_ENTITY_ID' : BXRS[voteId].entityId, 'RATING_VOTE_ACTION' : action},
		onsuccess: function(data)
		{
			BXRS[voteId].result.title = data['resultTitle'];
			BXRS[voteId].result.innerHTML = data['resultValue'];
			BX.removeClass(BXRS[voteId].result, data['resultStatus'] == 'minus' ? 'rating-vote-result-plus' : 'rating-vote-result-minus');
			BX.addClass(BXRS[voteId].result, data['resultStatus'] == 'minus' ? 'rating-vote-result-minus' : 'rating-vote-result-plus');
		
			Rating.UpdateStatus(voteId, button, action);
			BXRS[voteId].voteProcess = false;
		},
		onfailure: function(data)
		{
			BX.removeClass(button == 'minus' ? BXRS[voteId].buttonMinus : BXRS[voteId].buttonPlus,  'rating-vote-load');
		}
	});

	return false;
}

