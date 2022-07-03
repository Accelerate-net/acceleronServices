package com.accelerate.acceleronServices.api;



import com.accelerate.acceleronServices.reservation.dto.request.ReservationDto;
import com.accelerate.acceleronServices.reservation.dto.response.ApiResponse;
import com.accelerate.acceleronServices.reservation.dto.response.GenericResponse;
import com.accelerate.acceleronServices.reservation.model.ReservationEntity;
import com.accelerate.acceleronServices.reservation.service.ReservationService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.data.jpa.repository.Modifying;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
public class ReservationApi {

    @Autowired
    private ReservationService reservationService;

    @RequestMapping(method = RequestMethod.POST, value = "reservation/makeReservation")
    public ResponseEntity<?> getReservation(@RequestBody ReservationDto request) {
        ApiResponse<GenericResponse> response = reservationService.makeReservation(request);
        return ResponseEntity.ok().body(response);
    }


    @RequestMapping(method = RequestMethod.GET,value = "reservation/getReservation/{name}")
    public ResponseEntity<List<ReservationEntity>> getReservation(@PathVariable String name){
        return new ResponseEntity<List<ReservationEntity>>(reservationService.getReservation(name), HttpStatus.OK);

    }

    @RequestMapping(method = RequestMethod.GET,value = "reservation/getReservations")
    public ResponseEntity<List<ReservationEntity>> getAllReservation(){
        return new ResponseEntity<List<ReservationEntity>>(reservationService.getAllReservation(), HttpStatus.OK);
    }

    @RequestMapping(method = RequestMethod.DELETE, value = "reservation/deleteReservation/{name}")
    public ResponseEntity<?> deleteReservation(@PathVariable String name){
        ApiResponse<GenericResponse> response =  reservationService.deleteReservation(name);
        return ResponseEntity.ok().body(response);

    }

    @RequestMapping(method = RequestMethod.PUT, value = "reservation/updateReservation")
    public ResponseEntity<?> updateReservation(@RequestBody ReservationDto request){
        ApiResponse<GenericResponse> response = reservationService.updateReservation(request);
        return ResponseEntity.ok().body(response);

    }

}
