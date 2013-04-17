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
		}
  });

  // Load the plugin that provides the "uglify" task.
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-watch');

};
