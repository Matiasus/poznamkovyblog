/****************************************************************************
 Title	:   PWM Library
 Author:    Marian Hrinko <mato.hrinko@gmail.com>  http://poznamkovyblog.cekuj.net
 File:	    pwm.c 2016/04/15
 Software:  AVR-GCC 3.3 

 DESCRIPTION
       PWM generator with basic mode selector

 USAGE
       See the C include pwm.h file for a description of each function
       
*****************************************************************************/
#define F_CPU 8000000UL

#include <avr/io.h>
#include "pwm.h"

/**
 * Inicializacia PWM modu a preddelicky
 *
 * @param   mode - vyber modu        
 * @param   prescaler - vyber preddelicky
 * @return  void
*/
void pwm_select(int mode, int prescaler)
{
  PWM_MODE(mode);
  PWM_PRESCALER(prescaler);
}
