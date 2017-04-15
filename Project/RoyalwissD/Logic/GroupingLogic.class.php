<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-27
	 * Time: 10:56
	 */
	namespace RoyalwissD\Logic;

	use CMS\Logic\Session;
	use CMS\Logic\UserLogic;
	use CMS\Model\CMSModel;
	use General\Logic\Time;
	use General\Model\GeneralModel;
	use Quasar\Utility\StringPlus;

	class GroupingLogic extends RoyalwissDLogic{
		public function _initialize(){
			parent::_initialize();
		}

		/**
		 * 处理POST/GET请求
		 *
		 * @param string $type 请求类型
		 * @param array  $opt  相关参数
		 *
		 * @return mixed
		 */
		public function handlerRequest($type, $opt = []){
			switch($type){
				case 'batch_create':
					if(!UserLogic::isPermitted('SEVERAL-GROUPING.CREATE')) return [
						'status'   => false,
						'message'  => '您没有创建分组的权限',
						'__ajax__' => true
					];
					$post           = I('post.');
					$meeting_id     = I('get.mid', 0, 'int');
					$group_name_arr = explode(',', $post['group_name']);
					/** @var \RoyalwissD\Model\GroupingModel $group_model */
					$group_model = D('RoyalwissD/Grouping');
					$str_obj     = new StringPlus();
					$group_data  = [];
					foreach($group_name_arr as $group_name){
						$group_data[] = [
							'name'        => $group_name,
							'name_pinyin' => $str_obj->getPinyin($group_name, true, ''),
							'mid'         => $meeting_id,
							'capacity'    => $post['capacity'],
							'comment'     => $post['comment'],
							'creatime'    => Time::getCurrentTime(),
							'creator'     => Session::getCurrentUser()
						];
					}
					$result = $group_model->addAll($group_data);

					return $result ? [
						'status'   => true,
						'message'  => '创建成功',
						'__ajax__' => true
					] : [
						'status'   => false,
						'message'  => '创建失败',
						'__ajax__' => true
					];
				break;
				case 'create':
					if(!UserLogic::isPermitted('SEVERAL-GROUPING.CREATE')) return [
						'status'   => false,
						'message'  => '您没有创建分组的权限',
						'__ajax__' => true
					];
					$post       = I('post.');
					$meeting_id = I('get.mid', 0, 'int');
					/** @var \RoyalwissD\Model\GroupingModel $group_model */
					$group_model = D('RoyalwissD/Grouping');
					$str_obj     = new StringPlus();
					$result      = $group_model->create([
						'mid'         => $meeting_id,
						'name'        => $post['name'],
						'name_pinyin' => $str_obj->getPinyin($post['name'], true, ''),
						'capacity'    => $post['capacity'],
						'comment'     => $post['comment'],
						'creatime'    => Time::getCurrentTime(),
						'creator'     => Session::getCurrentUser()
					]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'delete': // 删除项目
					if(!UserLogic::isPermitted('SEVERAL-GROUPING.DELETE')) return [
						'status'   => false,
						'message'  => '您没有删除分组的权限',
						'__ajax__' => true
					];
					$id_str     = I('post.id', '');
					$meeting_id = I('get.mid', 0, 'int');
					$id_arr     = explode(',', $id_str);
					/** @var \RoyalwissD\Model\GroupingModel $group_model */
					$group_model = D('RoyalwissD/Grouping');
					$result      = $group_model->drop(['id' => ['in', $id_arr], 'mid' => $meeting_id]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'enable': // 启用项目
					if(!UserLogic::isPermitted('SEVERAL-GROUPING.ENABLE')) return [
						'status'   => false,
						'message'  => '您没有启用分组的权限',
						'__ajax__' => true
					];
					$id_str     = I('post.id', '');
					$meeting_id = I('get.mid', 0, 'int');
					$id_arr     = explode(',', $id_str);
					/** @var \RoyalwissD\Model\GroupingModel $group_model */
					$group_model = D('RoyalwissD/Grouping');
					$result      = $group_model->enable(['id' => ['in', $id_arr], 'mid' => $meeting_id]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'disable': // 禁用项目
					if(!UserLogic::isPermitted('SEVERAL-GROUPING.DISABLE')) return [
						'status'   => false,
						'message'  => '您没有禁用分组的权限',
						'__ajax__' => true
					];
					$id_str     = I('post.id', '');
					$meeting_id = I('get.mid', 0, 'int');
					$id_arr     = explode(',', $id_str);
					/** @var \RoyalwissD\Model\GroupingModel $group_model */
					$group_model = D('RoyalwissD/Grouping');
					$result      = $group_model->disable(['id' => ['in', $id_arr], 'mid' => $meeting_id]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'get_member':
					$meeting_id = I('get.mid', 0, 'int');
					$group_id   = I('post.id', 0, 'int');
					/** @var \RoyalwissD\Model\GroupingModel $group_model */
					$group_model = D('RoyalwissD/Grouping');
					$member_list = $group_model->getMember($meeting_id, $group_id);

					return array_merge($member_list, ['__ajax__' => true]);
				break;
				case 'add_member':
					if(!UserLogic::isPermitted('SEVERAL-GROUPING.ADD_MEMBER')) return [
						'status'   => false,
						'message'  => '您没有添加组员的权限',
						'__ajax__' => true
					];
					$meeting_id    = I('get.mid', 0, 'int');
					$group_id      = I('post.id', 0, 'int');
					$client_id_str = I('post.cid', '');
					$client_id     = explode(',', $client_id_str);
					/** @var \RoyalwissD\Model\GroupingModel $group_model */
					$group_model = D('RoyalwissD/Grouping');
					$result      = $group_model->addMember($meeting_id, $group_id, $client_id);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'remove_member':
					if(!UserLogic::isPermitted('SEVERAL-GROUPING.CLEAN_MEMBER')) return [
						'status'   => false,
						'message'  => '您没有删除组员的权限',
						'__ajax__' => true
					];
					$group_id   = I('post.id', 0, 'int');
					$client_id  = I('post.cid', 0, 'int');
					$meeting_id = I('get.mid', 0, 'int');
					/** @var \RoyalwissD\Model\GroupingModel $group_model */
					$group_model = D('RoyalwissD/Grouping');
					$result      = $group_model->removeMember($meeting_id, $group_id, $client_id);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'remove_all_member':
					if(!UserLogic::isPermitted('SEVERAL-GROUPING.CLEAN_MEMBER')) return [
						'status'   => false,
						'message'  => '您没有清空组员的权限',
						'__ajax__' => true
					];
					$group_id   = I('post.id', 0, 'int');
					$meeting_id = I('get.mid', 0, 'int');
					/** @var \RoyalwissD\Model\GroupingModel $group_model */
					$group_model = D('RoyalwissD/Grouping');
					$result      = $group_model->removeMember($meeting_id, $group_id, null, true);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'get_client':
					/** @var \RoyalwissD\Model\ClientModel $client_model */
					$client_model = D('RoyalwissD/Client');
					/** @var \RoyalwissD\Model\GroupingModel $group_model */
					$group_model = D('RoyalwissD/Grouping');
					$keyword     = I('post.keyword', '');
					$meeting_id  = I('get.mid', 0, 'int');
					//$group_id    = I('post.id', 0, 'int');
					$member_list = $group_model->getMember($meeting_id);
					$option      = [];
					if($member_list){
						$member_id_str = '';
						foreach($member_list as $member) $member_id_str .= "$member[id],";
						$member_id_str                                                    = trim($member_id_str, ',');
						$member_id_str                                                    = "($member_id_str)";
						$option[$client_model::CONTROL_COLUMN_PARAMETER_SELF['clientID']] = ['not in', $member_id_str];
					}
					if(!($keyword == '')) $option[CMSModel::CONTROL_COLUMN_PARAMETER['keyword']] = $keyword;
					$list = $client_model->getList(array_merge($option, [
						CMSModel::CONTROL_COLUMN_PARAMETER['status']                 => ['=', 1],
						$client_model::CONTROL_COLUMN_PARAMETER_SELF['reviewStatus'] => ['=', 1],
						$client_model::CONTROL_COLUMN_PARAMETER_SELF['signStatus']   => ['=', 1],
						$client_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID']    => $meeting_id,
						//						$client_model::CONTROL_COLUMN_PARAMETER_SELF['limit']        => [0, 20] // 分段读取
					]));

					return array_merge($list, ['__ajax__' => true]);
				break;
				case 'get_group':
					$group_id   = I('post.id', 0, 'int');
					$meeting_id = I('get.mid', 0, 'int');
					/** @var \RoyalwissD\Model\GroupingModel $group_model */
					$group_model = D('RoyalwissD/Grouping');
					if(!$group_model->fetch(['id' => $group_id, 'mid' => $meeting_id])) return [
						'status'   => false,
						'message'  => '找不到分组信息',
						'__ajax__' => true
					];
					$group_record = $group_model->getObject();

					return array_merge($group_record, ['__ajax__' => true]);
				break;
				case 'modify':
					if(!UserLogic::isPermitted('SEVERAL-GROUPING.MODIFY')) return [
						'status'   => false,
						'message'  => '您没有修改分组的权限',
						'__ajax__' => true
					];
					$group_id   = I('post.id', 0, 'int');
					$meeting_id = I('get.mid', 0, 'int');
					$post       = I('post.');
					/** @var \RoyalwissD\Model\GroupingModel $group_model */
					$group_model = D('RoyalwissD/Grouping');
					$str_obj     = new StringPlus();
					unset($post['id']);
					$result = $group_model->modify(['id' => $group_id, 'mid' => $meeting_id], [
						'name_pinyin' => $str_obj->getPinyin($post['name'], true, ''),
						'name'        => $post['name'],
						'capacity'    => $post['capacity'],
						'comment'     => $post['comment'],
					]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				default:
					return ['status' => false, 'message' => '缺少必要参数', '__ajax__' => true];
				break;
			}
		}

		/**
		 * 设定额外数据
		 *
		 * @param string $type 操作类型
		 * @param mixed  $data 处理数据
		 *
		 * @return mixed
		 */
		public function setData($type, $data){
			switch($type){
				case 'manage':
					foreach($data as $key => $group){
						$data[$key]['status_code'] = $data[$key]['status'];
						$data[$key]['status']      = GeneralModel::STATUS[$data[$key]['status']];
					}

					return $data;
				break;
			}
		}
	}