<?php

class SMAZ {

    //forward codebook
    private $_Smaz_cb;
    //reverse codebook
    private $_Smaz_rcb;

    //filling codebooks in the constructor
    function __construct() {

        //the reverse codebook
        $this->set_Smaz_rcb([
            " ", "the", "e", "t", "a", "of", "o", "and", "i", "n", "s", "e ", "r",
            " th", " t", "in", "he", "th", "h", "he ", "to", "\r\n", "l", "s ", "d",
            " a", "an", "er", "c", " o", "d ", "on", " of", "re", "of ", "t ", ", ",
            "is", "u", "at", "   ", "n ", "or", "which", "f", "m", "as", "it", "that",
            "\n", "was", "en", "  ", " w", "es", " an", " i", "\r", "f ", "g", "p",
            "nd", " s", "nd ", "ed ", "w", "ed", "http://", "for", "te", "ing", "y ",
            "The", " c", "ti", "r ", "his", "st", " in", "ar", "nt", ",", " to", "y",
            "ng", " h", "with", "le", "al", "to ", "b", "ou", "be", "were", " b",
            "se", "o ", "ent", "ha", "ng ", "their", "\"", "hi", "from", " f", "in ",
            "de", "ion", "me", "v", ".", "ve", "all", "re ", "ri", "ro", "is ", "co",
            "f t", "are", "ea", ". ", "her", " m", "er ", " p", "es ", "by", "they",
            "di", "ra", "ic", "not", "s, ", "d t", "at ", "ce", "la", "h ", "ne",
            "as ", "tio", "on ", "n t", "io", "we", " a ", "om", ", a", "s o", "ur",
            "li", "ll", "ch", "had", "this", "e t", "g ", "e\r\n", " wh", "ere",
            " co", "e o", "a ", "us", " d", "ss", "\n\r\n", "\r\n\r", "=\"", " be",
            " e", "s a", "ma", "one", "t t", "or ", "but", "el", "so", "l ", "e s",
            "s,", "no", "ter", " wa", "iv", "ho", "e a", " r", "hat", "s t", "ns",
            "ch ", "wh", "tr", "ut", "/", "have", "ly ", "ta", " ha", " on", "tha",
            "-", " l", "ati", "en ", "pe", " re", "there", "ass", "si", " fo", "wa",
            "ec", "our", "who", "its", "z", "fo", "rs", ">", "ot", "un", "<", "im",
            "th ", "nc", "ate", "><", "ver", "ad", " we", "ly", "ee", " n", "id",
            " cl", "ac", "il", "</", "rt", " wi", "div", "e, ", " it", "whi", " ma",
            "ge", "x", "e c", "men", ".com"
        ]);

        //filling the forward codebook
        $this->set_Smaz_cb(array_flip($this->get_Smaz_rcb()));
    }

    //reverse codebook setter, no apparent reason for a getter
    private function set_Smaz_cb($input_array) {
        $this->_Smaz_cb = $input_array;
    }

    //reverse codebook setter
    private function set_Smaz_rcb($input_array) {
        $this->_Smaz_rcb = $input_array;
    }

    //reverse codebook getter
    private function get_Smaz_rcb() {
        return $this->_Smaz_rcb;
    }

    //prepare a flush if we reached the flush length limit
    private function flush_verbatim($verbatim) {
        $output = [];

        if (strlen($verbatim) > 1) {
            array_push($output, chr(255));
            array_push($output, chr(strlen($verbatim) - 1));
        }
        else
            array_push($output, chr(254));

        $verbatim_length = strlen($verbatim);
        for ($i = 0; $i < $verbatim_length; $i++)
            array_push($output, $verbatim[$i]);

        return $output;
    }

    //compressor
    public function compress($input) {

        $verbatim = '';
        $output = [];
        $input_index = 0;
        $input_len = strlen($input);

        while ($input_index < $input_len) {
            $encoded = false;
            $i = 7;
            if ($input_len - $input_index < 7) {
                $i = $input_len - $input_index;
            }
            for ($i = $j = $i; $i <= 0 ? $j < 0 : $j > 0; $i = $i <= 0 ? ++$j : --$j) {

                if (isset($this->_Smaz_cb[substr($input, $input_index, $i)]))
                    $code = $this->_Smaz_cb[substr($input, $input_index, $i)];
                else
                    $code = null;

                if ($code != null) {
                    if ($verbatim) {
                        $output = array_merge($output, $this->flush_verbatim($verbatim));
                        $verbatim = '';
                    }
                    array_push($output, chr($code));
                    $input_index += $i;
                    $encoded = true;
                    break;
                }
            }
            if (!$encoded) {
                $verbatim .= $input[$input_index];
                $input_index++;
                if (strlen($verbatim) === 256) {
                    $output = array_merge($output, $this->flush_verbatim($verbatim));
                    $verbatim = '';
                }
            }
        }
        if ($verbatim) {
            $output = array_merge($output, $this->flush_verbatim($verbatim));
        }
        return implode('', $output);
    }

    //decompressor
    public function decompress($str_input) {
        $output = '';
        $results = [];
        for ($i = $j = 0, $_ref = strlen($str_input); 0 <= $_ref ? $j < $_ref : $j > $_ref; $i = 0 <= $_ref ? ++$j : --$j) {
            array_push($results, ord(substr($str_input, $i, 1)));
        }
        $input = $results;
        $i = 0;
        while ($i < count($input)) {
            if ($input[$i] === 254) {
                if ($i + 1 > count($input)) {
                    throw new Exception('malformed Smaz string');
                }
                $output .= $str_input[$i + 1];
                $i += 2;
            } else if ($input[$i] === 255) {
                if ($i + $input[$i + 1] + 2 >= strlen($input)) {
                    throw new Exception('malformed Smaz string');
                }
                for ($j = $j = 0, $_ref = $input[$i + 1] + 1; 0 <= $_ref ? $j < $_ref : $j > $_ref; $j = 0 <= $_ref ? ++$j : --$j) {
                    $output .= $str_input[$i + 2 + $j];
                }
                $i += 3 + $input[$i + 1];
            } else {
                $output .= $this->_Smaz_rcb[$input[$i]];
                $i++;
            }
        }
        return $output;
    }

}

//provided just for the sake of original library's compatibilty
function smaz_compress($input) {
    $smaz_inst = new SMAZ();
    return $smaz_inst->compress($input);
}

function smaz_decompress($input) {
    $smaz_inst = new SMAZ();
    return $smaz_inst->decompress($input);
}
?>
