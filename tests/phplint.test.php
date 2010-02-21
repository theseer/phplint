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

namespace TheSeer\Tools\Tests {

   use TheSeer\Tools;

use TheSeer\Tools\PHPLint;

   /**
    * Unit tests for phplint class
    *
    * @author     Arne Blankerts <arne@blankerts.de>
    * @copyright  Arne Blankerts <arne@blankerts.de>, All rights reserved.
    */
   class PHPLintTest extends \PHPUnit_Framework_TestCase {

      /**
       * @expectedException \TheSeer\Tools\PHPLintException
       */
      public function testWrongBinaryPathThrowsException() {
         $x = new PHPLint('/does/not/exist');
      }

      /**
       * @expectedException \TheSeer\Tools\PHPLintException
       */
      public function testNotExecutableBinaryPathThrowsException() {
         $x = new PHPLint('/etc/passwd');
      }

      /**
       * @covers \TheSeer\Tools\PHPLint::findBinary
       * @covers \TheSeer\Tools\PHPLint::getBinary
       */
      public function testFindBinary() {
         $x = new PHPLint();
         $this->assertEquals($x->getBinary(),'/usr/bin/php');
      }

      public function testLintOnValidCodeReturnsTrue() {
         $x = new PHPLint();
         $this->assertTrue($x->lintString('<?php phpinfo(); ?>'));
      }

      public function testLintOnValidFileReturnsTrue() {
         $x = new PHPLint();
         $this->assertTrue($x->lintFile(__DIR__.'/_data/valid.php'));
      }

      /**
       * @expectedException \TheSeer\Tools\PHPLintException
       */
      public function testLintOnNonExistingFileThrowsException() {
         $x = new PHPLint();
         $x->lintFile('/does/not/exist');
      }

      /**
       * @expectedException \TheSeer\Tools\PHPLintException
       */
      public function testLintOnEmptyStringThrowsException() {
         $x = new PHPLint();
         $x->lintString('');
      }

      public function testLintWithParseErrorReturnsFalse() {
         $x = new PHPLint();
         $this->assertFalse($x->lintString('<?php missingBracket( ?>'));
      }

      public function testParseErrorObjectForLintWithParseError() {
         $x = new PHPLint();
         $x->lintString('<?php missingBracket( ?>');
         $error = $x->getError();
         $this->assertTrue($error instanceof \TheSeer\Tools\parseError);
         $this->assertEquals($error->line, 1);
         $this->assertEquals($error->file, '-');
         $this->assertEquals($error->type, 'PHP Parse error');
         $this->assertEquals($error->message, "syntax error, unexpected ';', expecting ')'");
      }



   }

}