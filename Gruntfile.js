module.exports = function(grunt) {
  grunt.initConfig({
    watch: {
      files: ['themes/addons/**/*.less'],
      tasks: 'less:debug'
    },
    less: {
      debug: {
        options: {
          paths: ['themes/addons/less/'],
          yuicompress: false
        },
        files: {
          "themes/addons/css/addons.css": "themes/addons/less/addons.less"
        }
      },
      production: {
        options: {
          paths: ['themes/addons/less/'],
          yuicompress: true
        },
        files: {
          "themes/addons/css/addons.css": "themes/addons/less/addons.less"
        }
      }
    },
    uglify: {
      bootstrap: {
        files: {
          "themes/addons/bootstrap/js/bootstrap.min.js": "themes/addons/bootstrap/js/bootstrap.js"
        }
      }
    }
  });

  // Load the plugin that provides the "uglify" task.
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-uglify');
};
