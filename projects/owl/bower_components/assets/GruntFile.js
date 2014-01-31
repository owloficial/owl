module.exports = function(grunt) {
  //Pareiq aqui
  var srcPath = 'js/**/*.js';
  var specsPath = 'specs/**/*spec*.js';
  var helperPath = 'specs/helpers/*.js';
  grunt.initConfig({
    // compass: {
    //   dist: {
    //     options: {
    //       config: 'sass/config.rb',
    //       cssDir: 'css'
    //     }
    //   }
    // },
    compass: {
      dist: {
        options: {
          force: true,
          config: 'config.rb',
          outputStyle: 'compressed'
        }
      }
    },

    concat: {
      options: {
        separator: ';'
      },
      dist: {
        src: ['libs/**/*.js', srcPath],
        dest: 'dist/main.js'
      }
    },    
    uglify: {
      options: {
        mangle: false,
        compress: false,
        report : 'min',
        // the banner is inserted at the top of the output
        banner: '/*! <%= grunt.template.today("dd-mm-yyyy") %> */\n'
      },
      dist: {
        files: { 
          'dist/main.min.js': ['libs/**/*.js', srcPath]
        }
      }
    },
   jshint: {
      all: ['Gruntfile.js', 'lib/**/*.js', specsPath, srcPath]
    },    
    jasmine : {
      pivotal:{
          // Your project's source files
          src : [srcPath],
          // Your Jasmine spec files
          options: {
            vendor : ['libs/**/*.js'],
            specs : specsPath,
            // Your spec helper files
            helpers : helperPath
          }
      }
    },
    watch: {
       pivotal : {
            files: [specsPath, srcPath], 
            tasks: ['jshint','jasmine', 'concat', 'uglify']
        }
    },
      compass_watch: {
        css: {
          files: ['../assets/sass/**/*'],
          tasks: [ 'compass' ]
        }
      }
  });
  
  grunt.loadNpmTasks('grunt-contrib-jasmine');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-compass');
  // Default task.
  grunt.registerTask('test', ['jshint', 'jasmine']);
  grunt.registerTask('default', ['test']);
  grunt.registerTask('compass_watch', ['compass']);
};