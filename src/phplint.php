<?php
/**
 * Copyright (c) 2010 Arne Blankerts <arne@blankerts.de>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 *   * Neither the name of Arne Blankerts nor the names of contributors
 *     may be used to endorse or promote products derived from this software
 *     without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT  * NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER ORCONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    PHPLint
 * @author     Arne Blankerts <arne@blankerts.de>
 * @copyright  Arne Blankerts <arne@blankerts.de>, All rights reserved.
 * @license    BSD License
 * @link       http://github.com/theseer/phplint
 */

namespace TheSeer\Tools {

   require 'parseerror.php';

   /**
    * Helper class to run a lint check on php code and get possible errors in useable form
    *
    * @author     Arne Blankerts <arne@blankerts.de>
    * @copyright  Arne Blankerts <arne@blankerts.de>, All rights reserved.
    * @version    Release: %version%
    */
   class PHPLint {

      /**
       * Path to php binary to be used
       *
       * @var string
       */
      protected $phpBinary;

      /**
       * Reference to parseError object in case of lint error
       *
       * @var parseError
       */
      protected $error;

      /**
       * PHPLint Constructor method
       *
       * @param string $php Path to php binary to use
       *
       */
      public function __construct($php = null) {
         if (!is_null($php) && (!file_exists($php) || !is_executable($php))) {
            throw new PHPLintException("Specified binary '$php' is not valid (not found or not executable)",PHPLintException::InvalidBinary);
         }
         $this->phpBinary = $php ? $php : $this->findBinary();
      }

      /**
       * Helper method to allow potentially a sofisticated search for a php binary,
       * this implementation is rather simple minded though
       *
       * @return string
       */
      protected function findBinary() {
         if (PHP_OS == 'win') {
            return 'c:\php\php.exe';
         } else {
            return trim(shell_exec('which php'));
         }
      }

      /**
       * Getter for php binary path in use
       *
       * @return string
       */
      public function getBinary() {
         return $this->phpBinary;
      }

      /**
       * Getter for parseError object in case of lint errors
       *
       * @return Tools\parseError
       */
      public function getError() {
         return $this->error;
      }

      /**
       * Run lint check on a file
       *
       * @param string $fname File to run lint check on
       *
       * @return boolean  True for no lint errors, false otherwise
       */
      public function lintFile($fname) {
         if (!file_exists($fname)) {
            throw new PHPLintException("File '$fname' not found", PHPLintException::FileNotFound);
         }
         $code = file_get_contents($fname);
         return $this->lintString($code, $fname);
      }

      /**
       * Run lint check on a given string of php code
       *
       * @param string $code  String containg php code to run lint on
       * @param string $fname Optional Filename to use in error object
       *
       * @return boolean  True for no lint errors, false otherwise
       */
      public function lintString($code, $fname = null) {

         if ($code == '') {
            throw new PHPLintException('Cannot run lint on empty string', PHPLintException::EmptyString);
         }

         $dsp = array(
            0 => array("pipe", "r"),
            1 => array("pipe", "w"),
            2 => array("pipe", "w")
         );

         $process = proc_open($this->phpBinary . ' -l', $dsp, $pipes);
         if (!is_resource($process)) {
            throw new PHPLintException("Opening php binary ({$this->phpBinary}) for linting failed.", PHPLintException::BinaryOpenFailed);
         }

         fwrite($pipes[0], $code);
         fclose($pipes[0]);

         $stdout = stream_get_contents($pipes[1]);
         fclose($pipes[1]);

         $stderr = stream_get_contents($pipes[2]);
         fclose($pipes[2]);

         $rc = proc_close($process);

         if ($rc == 255) {
            $this->error = new parseError($stderr,$fname);
            return false;
         }

         $this->error = null;
         return true;
      }

   }

   class PHPLintException extends \Exception {
      const InvalidBinary    = 1;
      const BinaryOpenFailed = 2;
      const FileNotFound     = 3;
      const EmptyString      = 4;
   }
}