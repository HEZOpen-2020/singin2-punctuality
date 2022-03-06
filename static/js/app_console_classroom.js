$(() => {
    G.classroom = {};
    
    var last_singing_lesson = null;
    
    var markings = {};
    var default_mark = 'card';

    var send_dialog = new mdui.Dialog('.singin-classroom-send-dialog', {modal: true, closeOnEsc: false});
    var send_state = true;
    var sending = false;
    
    var determine_state = (obj, mark = true) => {
        if(markings[obj.oid] == 'revoke' && mark) {
            return 'revoke';
        }
        if(obj.dk_time != null) {
            return obj.dk_method;
        }
        if(markings[obj.oid] && mark) {
            if(markings[obj.oid] == 'face') {
                return 'mark-face';
            } else if(markings[obj.oid] == 'card') {
                return 'mark-card';
            } else {
                return 'revoke';
            }
        }
        if(obj.dk_exclude) {
            return 'exclude';
        }
        if(obj.dk_leave) {
            return 'leave';
        }
        return 'none';
    };
    var state_message = (obj) => {
        var state = determine_state(obj, true);
        if(state == 'revoke') {
            return '标记为撤销签到';
        }
        if(state == 'face' || state == 'card') {
            var dk_time = new Date(obj.dk_time * 1000);
            return (obj.dk_method == 'face' ? '人脸' : '刷卡') + ' · ' + /*date_format_date(dk_time) + ' ' +*/ date_format_seconds(dk_time);
        }
        if(state == 'exclude') {
            return '考勤开始时不在校内';
        }
        if(state == 'leave') {
            return '请假';
        }
        if(state == 'mark-face') {
            return '标记为人脸签到';
        }
        if(state == 'mark-card') {
            return '标记为刷卡签到';
        }
        return '未签到';
    };
    var state_icon = (obj) => {
        var state = determine_state(obj);
        if(state == 'face' || state == 'card') {
            return 'done';
        }
        if(state == 'exclude') {
            return 'block';
        }
        if(state == 'leave') {
            return 'directions_run';
        }
        if(state == 'none') {
            return 'clear';
        }
        if(state == 'mark-card' || state == 'mark-face') {
            return 'local_offer';
        }
        if(state == 'revoke') {
            return 'backspace';
        }
        return '';
    };
    var action_title = (obj) => {
        var state = determine_state(obj);
        if(state == 'face' || state == 'card') {
            return 'Revoke';
        }
        if(state == 'mark-card' || state == 'mark-face' || state == 'revoke') {
            return 'Cancel';
        }
        return 'Singin';
    };
    
    var actuate_all = () => {
        $('.singin-lesson-name').text(G.classroom.singing_lesson.name);

        var consistency = true;
        (() => {
            var item_list = $('.singin-classroom-student-o');
            if(item_list.length != G.classroom.singing_students.length) {
                consistency = false;
                return;
            }
            for(let i = 0; i < item_list.length; i++) {
                var item = G.classroom.singing_students[i];
                if(!item || item.oid != $(item_list[i]).attr('data-singin-classroom-oid')) {
                    consistency = false;
                    break;
                }
            }
        })();

        if(!consistency) {
            $('.singin-classroom-student-o').remove();
            $('.singin-student-list').html('');
        }

        var has_student = false;
        for(let idx in G.classroom.singing_students) {
            if(idx * 1 != idx) continue;

            let student = G.classroom.singing_students[idx];
            let dk_state = determine_state(student);
            let dk_message = state_message(student);
            let stu_card = null;
            
            if(!consistency) {
                stu_card = $(`
                    <div class="mdui-col-xs-6 mdui-col-sm-4 mdui-col-md-3 mdui-col-lg-2 singin-student-o singin-classroom-student-o">
                        <div class="mdui-card singin-student-i" data-singin-state="">
                            <div class="mdui-card-content">
                                <div class="singin-student-name"></div>
                                <div class="singin-student-status"></div>
                            </div>
                            <div class="mdui-card-actions">
                                <button class="mdui-btn mdui-ripple singin-student-action"></button>
                                <button class="mdui-btn mdui-btn-icon mdui-float-right singin-student-icon-btn">
                                    <i class="mdui-icon material-icons singin-student-icon"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `).attr('data-singin-classroom-oid', student.oid);
                $('.singin-student-list').append(stu_card);
            }
            
            $(`[data-singin-classroom-oid="${student.oid}"] .singin-student-i`).attr('data-singin-state', dk_state);
            $(`[data-singin-classroom-oid="${student.oid}"] .singin-student-name`).text(student.name);
            $(`[data-singin-classroom-oid="${student.oid}"] .singin-student-status`).text(dk_message);
            let $action_btn = $(`[data-singin-classroom-oid="${student.oid}"] .singin-student-action`);
            $action_btn.text(action_title(student));
            if(action_title(student) == 'Revoke' && (!G.system_status || !G.system_status.classroom_allow_revoke)) {
                $action_btn.disabled(true);
            } else {
                $action_btn.disabled(false);
            }
            $(`[data-singin-classroom-oid="${student.oid}"] .singin-student-icon`).text(state_icon(student));

            if(!consistency) {
                $(`[data-singin-classroom-oid="${student.oid}"] .singin-student-action`).on('click', () => {
                    let student = G.classroom.singing_students[idx];
                    var state = determine_state(student);
                    if(state == 'revoke' || state == 'mark-face' || state == 'mark-card') {
                        delete markings[student.oid];
                    } else if(state == 'card' || state == 'face') {
                        markings[student.oid] = 'revoke';
                    } else {
                        markings[student.oid] = default_mark;
                    }
                    actuate_all();
                });
                $(`[data-singin-classroom-oid="${student.oid}"] .singin-student-icon-btn`).on('click', () => {
                    let student = G.classroom.singing_students[idx];
                    var state = determine_state(student);
                    if(state == 'mark-face') {
                        markings[student.oid] = 'card';
                    } else if(state == 'mark-card') {
                        markings[student.oid] = 'face';
                    }
                    actuate_all();
                });
            }
            // 检查标记相容性
            var dk_state2 = determine_state(student, false);
            if(dk_state2 == 'face' || dk_state2 == 'card') {
                if(markings[student.oid] == 'mark-card' || markings[student.oid] == 'mark-face') {
                    delete markings[student.oid];
                }
            } else {
                if(markings[student.oid] == 'revoke') {
                    delete markings[student.oid];
                }
            }

            has_student = true;
        }
        if(!has_student) {
            $('.singin-student-list').html('<div class="mdui-col-xs-12">未发现任何考勤人员</div>');
        }
    };
    $('.singin-classroom-action-refresh').on('click', async function() {
        $(this).disabled(true);
        var state = await refresh();
        $(this).disabled(false);
        if (state) {
            mdui.snackbar('数据已更新。', {timeout: 500});
        } else {
            mdui.snackbar('刷新失败。', {timeout: 2000});
        }
    });
    $('.singin-classroom-action-clear').on('click', async function() {
        var state = await mdui.confirm_async('确定清空所有未提交的标记？', '清空标记');
        if(state) {
            markings = [];
            actuate_all();
            mdui.snackbar('已清空标记。', {timeout: 500});
        }
    });
    $('.singin-classroom-action-markall').on('click', async function() {
        var state = await mdui.confirm_async('确定标记所有同学签到（不在校内的除外）？<br />请不要标记不该签到的同学！', '标记全部');
        if(state) {
            if(G.classroom.singing_students) {
                for(let student of G.classroom.singing_students) {
                    let state = determine_state(student);
                    if(['none', 'leave'].indexOf(state) != -1) {
                        markings[student.oid] = default_mark;
                    }
                }
                actuate_all();
                mdui.snackbar('标记完成。', {timeout: 500});
            } else {
                mdui.snackbar('人员信息尚未获取。', {timeout: 2000});
            }
        }
    });
    $('.singin-classroom-action-send').on('click', async function() {
        var marked_state = ['mark-card', 'mark-face', 'revoke'];
        if(G.classroom.singing_students) {
            var dk_count = 0;
            var revoke_count = 0;
            var pending_changes = [];
            for(let student of G.classroom.singing_students) {
                let state = determine_state(student);
                if(marked_state.indexOf(state) != -1) {
                    if(state == 'revoke') {
                        revoke_count += 1;
                    } else {
                        dk_count += 1;
                    }
                    pending_changes.push(student);
                }
            }
            pending_changes.sort(() => (Math.random() - 0.5));
            if(dk_count + revoke_count == 0) {
                mdui.snackbar('没有待提交的信息。', {timeout: 2000});
                return;
            }
            let text_frag = '';
            if(dk_count > 0) {
                text_frag += `<li>补签 ${dk_count} 人</li>`;
            }
            if(revoke_count > 0) {
                text_frag += `<li>撤销 ${revoke_count} 人</li>`;
            }
            var state = await mdui.confirm_async('确定提交所有更改？<br /><ul>' + text_frag + '</ul>请注意已提交的签到信息将无法撤销，因此请确认信息无误。<br />', '提交更改');
            if(!state) return;

            $('.singin-classroom-action-send').disabled(true);

            send_dialog.open();
            $('.singin-classroom-send-process-y').text(dk_count + revoke_count);
            let i = 0;
            send_state = true;
            sending = true;
            $('.singin-classroom-send-break').disabled(false);
            var success_count = 0;
            var failure_count = 0;
            for(let student of pending_changes) {
                let state = determine_state(student);
                if(marked_state.indexOf(state) != -1) {
                    i += 1;
                    $('.singin-classroom-send-process-x').text(i);
                    $('.singin-classroom-send-name').text(student.name);
                    var data = await post_json(G.basic_url + 'api/classroom/submit-marking', {
                        lesson: G.system_status.singing_lesson_id,
                        oid: student.oid,
                        mark: markings[student.oid]
                    });
                    if(!data || !data.success) {
                        failure_count += 1;
                    } else {
                        success_count += 1;
                        delete markings[student.oid];
                    }
                    // 已阻断
                    if(!send_state) {
                        break;
                    }
                }
            }
            send_dialog.close();
            mdui.snackbar(`更改提交完成，${success_count} 个成功，${failure_count} 个失败。`, {timeout: 4000});
            sending = false;

            // actuate_all();
            await refresh();

            if(success_count > 0) {
                if(await mdui.confirm_async('更改提交完成。<br />为确保更改生效，建议立即重启终端程序。<br />是否立即重启？', '提交完成')) {
                    var data = await fetch_json(G.basic_url + 'api/system/terminal/restart');
                    if(!data) {
                        mdui.snackbar('异常无法操作。', {timeout: 2000});
                    } else if(data.code == 500) {
                        mdui.snackbar('桌面启动器不在运行，因而无法操作。', {timeout: 2000});
                    } else {
                        mdui.snackbar('已重启终端程序。', {timeout: 2000});
                    }
                }
            }

            $('.singin-classroom-action-send').disabled(false);
        } else {
            mdui.snackbar('人员信息尚未获取。', {timeout: 2000});
        }
    });
    $('.singin-classroom-send-break').on('click', () => {
        $('.singin-classroom-send-break').disabled(true);
        send_state = false;
    });
    
    var refresh = async () => {
        if(!G.system_status) {
            return false;
        }
        var singing_lesson = G.system_status.singing_lesson_id;
        if(last_singing_lesson == null && singing_lesson != null) {
            var data = await fetch_json(G.basic_url + 'api/classroom/singing-lesson');
            if(!data || !data.success) {
                return false;
            }
            $('.singin-classroom-empty').hide();
            $('.singin-classroom-active').show();
            G.classroom.singing_lesson = data.data;
        }
        if(last_singing_lesson != null && singing_lesson == null) {
            $('.singin-classroom-empty').show();
            $('.singin-classroom-active').hide();
            G.classroom.singing_students = [];
            actuate_all();
            G.classroom.singing_students = null;
        }
        if(singing_lesson) {
            var data = await fetch_json(G.basic_url + 'api/classroom/singing-students');
            if(!data || !data.success) {
                return false;
            }
            G.classroom.singing_students = data.data;
            actuate_all();
        }
        last_singing_lesson = singing_lesson;

        return true;
    };
    
    setInterval(() => {
        if(!sending) refresh();
    }, 6000);
    $(document).on('data_statusinit', () => {
        refresh();
    });
});
