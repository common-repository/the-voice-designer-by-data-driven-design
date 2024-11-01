(function (wp) {
  var registerPlugin = wp.plugins.registerPlugin;
  var Fragment = wp.element.Fragment;
  var PluginSidebar = wp.editPost.PluginSidebar;
  var PluginSidebarMoreMenuItem = wp.editPost.PluginSidebarMoreMenuItem;
  var el = wp.element.createElement;
  var Textarea = wp.components.TextareaControl;
  var withSelect = wp.data.withSelect
  var withDispatch = wp.data.withDispatch;;

  var dddLogo = el('img', { width: 24, src: DddVoiceDesigner.logoUrl });

  var mapSelectToProps = function (select) {
    return {
      metaFieldValue: select('core/editor')
        .getEditedPostAttribute('meta')['ddd_voice_plugin_voice_content']
    };
  }

  var mapDispatchToProps = function (dispatch) {
    return {
      setMetaFieldValue: function (value) {
        dispatch('core/editor').editPost({
          meta: {
            ddd_voice_plugin_voice_content: value
          }
        });
      }
    }
  }

  var voiceContentField = function (props) {
    return el(Textarea, {
      label: 'Voice content for this page',
      value: props.metaFieldValue,
      onChange: function (content) {
        props.setMetaFieldValue(content);
      }
    });
  };
  var voiceContentFieldWithData = withSelect(mapSelectToProps)(voiceContentField);
  var voiceContentFieldWithDataAndActions = withDispatch(mapDispatchToProps)(voiceContentFieldWithData);

  registerPlugin('ddd-voice-sidebar', {
    render: function () {
      var sidebarClass = {
        className: 'ddd-voice-plugin-sidebar'
      };

      return el(Fragment, {}, [
        el(
          PluginSidebar,
          {
            name: 'ddd-voice-sidebar',
            icon: dddLogo,
            title: 'The Voice Designer',
          },
          el('div', sidebarClass, [
            el(voiceContentFieldWithDataAndActions),
            el('a', { href: '/wp-admin/admin.php?page=ddd-voice' }, 'Return to DDD Voice Designer')
          ])
        ),
        el(
          PluginSidebarMoreMenuItem,
          {
            target: 'ddd-voice-sidebar',
            icon: dddLogo,
          },
          'Data Driven Design',
        )
      ]);
    },
  });
})(window.wp);
