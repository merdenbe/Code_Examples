/* library.c: string utilities library */

#include "str.h"

#include <ctype.h>
#include <string.h>
#include <stdio.h>

/**
 * Convert all characters in string to lowercase.
 * @param   s       String to convert
 * @return          Pointer to beginning of modified string
 **/
char *	str_lower(char *s) {
    for (char *c = s; *c; c++) {
        if (*c >= 65 && *c <= 90) {
            *c = *c + 32; 
        }
    }
    return s;
}

/**
 * Convert all characters in string to uppercase.
 * @param   s       String to convert
 * @return          Pointer to beginning of modified string
 **/
char *	str_upper(char *s) {
    for (char *c = s; *c; c++) {
        if (*c >= 97 && *c <= 122) {
            *c = *c -32;
        }
    }
    return s;
}

/**
 * Returns whether or not the 's' string starts with given 't' string.
 * @param   s       String to search within
 * @param   t       String to check for
 * @return          Whether or not 's' starts with 't'
 **/
bool	str_startswith(const char *s, const char *t) {
   
    if (!(*t)) {
        return true;
    }

    while (*s && *s == *t) {
        t++;
        s++;
        if (!(*t)) {
            return true;
        }
    }
    
    return false;
    
}

/**
 * Returns whether or not the 's' string ends with given 't' string.
 * @param   s       String to search within
 * @param   t       String to check for
 * @return          Whether or not 's' ends with 't'
 **/
bool	str_endswith(const char *s, const char *t) {
    
    size_t slength = strlen(s);
    size_t tlength = strlen(t);
    
    if (!(*t)) {
        return true;
    }
    if (tlength > slength) {
        return false;
    }

    for (size_t i = 0; i < (slength - tlength); i++) {
        s++;
    }

    while (*s && *s == *t) {
        t++;
        s++;
        if (!(*t)) {
            return true;
        }
    }

    return false;
}

/**
 * Removes trailing newline (if present).
 * @param   s       String to modify
 * @return          Pointer to beginning of modified string
 **/
char *	str_chomp(char *s) {
    
    char *s_initial = s;
    size_t slength = strlen(s);
     
    if (slength == 0) {
        return s;
    }

    for (size_t i = 0; i < (slength-1); i++) {
        s++;
    }
    
    if (*s == '\n') {
        *s = '\0';
    }

    return s_initial;
}

/**
 * Removes whitespace from front and back of string (if present).
 * @param   s       String to modify
 * @return          Pointer to beginning of modified string
 **/
char *	str_strip(char *s) {
    char *s_initial = s; 
    while (isspace(*s_initial)) {
        s_initial++;
    }

    while (*s) {
        s++;
    }

    s--;
    while (*s == 32) {
        *s = '\0';
    }

    return s_initial;
}

/**
 * Reverses a string.
 * @param   s       String to reverse
 * @return          Pointer to beginning of modified string
 **/
char *	str_reverse(char *s) {
    
    if (strlen(s) < 2) {
        return s;
    }
    
    char *start = s;
    char *end   = s;

    while (*end) {
        end++;
    }
    end--;

    while (end > start) {
        char tmp = *start;
        *start = *end;
        *end = tmp;
        start++;
        end--;
    }

    return s;
}

/**
 * Replaces all instances of 'from' in 's' with corresponding values in 'to'.
 * @param   s       String to translate
 * @param   from    String with letter to replace
 * @param   to      String with corresponding replacment values
 * @return          Pointer to beginning of modified string
 **/
char *	str_translate(char *s, char *from, char *to) {

    char *s_initial = s;
    
    size_t from_len = strlen(from);
    size_t to_len    = strlen(to);

    if (from_len == 0 || to_len == 0) {
        return s_initial;
    }

    int trans_table[128] = {0};
    for (int i = 0; i < 128; i++) {
        trans_table[i] = i;
    }

    while (*from) {
        trans_table[(int)*from] = *to;
        from++;
        to++;
    }

    while (*s) {
        *s = trans_table[(int)*s];
        s++;
    }


    return s_initial;
}

/**
 * Converts given string into an integer.
 * @param   s       String to convert
 * @param   base    Integer base
 * @return          Converted integer value
 **/
int	str_to_int(const char *s, int base) {
    
    int power = 1;
    int sum = 0;
    const char *curr = s + strlen(s) - 1;

    while (curr >= s) {
        if (isdigit(*curr)) {
            sum += (((int)*curr - 48)*power);
        }
        else {
            sum += (((int)toupper(*curr) - 55)*power);
        }
        power *= base;
        curr--;
    }

    return sum;

}

/* vim: set sts=4 sw=4 ts=8 expandtab ft=c: */
