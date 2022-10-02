<?php

namespace enterprise\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Comment;

/**
 * CommentSearch represents the model behind the search form about `common\models\Comment`.
 */
class CommentSearch extends Comment
{
	public $r_filter;
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'from_id', 'target_id', 'parent_comment_id', 'approved_by', 'banned_by'], 'integer'],
            [['datetime', 'ban_reason', 'text'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Comment::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'datetime' => $this->datetime,
            'from_id' => $this->from_id,
            'target_id' => $this->target_id,
            'parent_comment_id' => $this->parent_comment_id,
            'approved_by' => $this->approved_by,
            'banned_by' => $this->banned_by,
        ]);

        $query->andFilterWhere(['like', 'ban_reason', $this->ban_reason])
            ->andFilterWhere(['like', 'text', $this->text]);

            
        if(!empty($_REQUEST['CommentSearch']) && !empty($_REQUEST['CommentSearch']['r_filter'])){
          	//Doctor name or Patient name
           	$filter = trim($_REQUEST['CommentSearch']['r_filter']);
           	$query->andWhere('CONCAT(user1.first_name, user1.last_name, user2.first_name, user2.last_name, comment.text) ILIKE \'%'. $filter .'%\'');
           	$query
           	->leftJoin(['user1' => 'user'], 'user1.id = comment.from_id')
           	->leftJoin(['user2' => 'user'], 'user2.id = comment.target_id');
        }
           
        return $dataProvider;
    }
}
