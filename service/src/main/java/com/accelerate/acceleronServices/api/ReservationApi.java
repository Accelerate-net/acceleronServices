package com.accelerate.acceleronServices.api;


import com.accelerate.acceleronServices.common.ErrorConstants;
import com.accelerate.acceleronServices.common.Utils;
import com.accelerate.acceleronServices.reservation.dto.request.ReservationRequestDto;
import com.accelerate.acceleronServices.reservation.dto.response.ApiResponse;
import com.accelerate.acceleronServices.reservation.dto.response.GenericResponse;
import com.accelerate.acceleronServices.reservation.model.ReservationEntity;
import com.accelerate.acceleronServices.reservation.service.ReservationRequestService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.text.ParseException;
import java.util.List;
import java.util.Optional;


@RestController
@RequestMapping("/reservation")
public class ReservationApi {

    @Autowired
    private ReservationRequestService reservationRequestService;



    @PostMapping()
    public ResponseEntity<?> makeReservation(@RequestBody ReservationRequestDto request) throws ParseException {

        String errorMessage = "";

        //Verifying userName
        if(!Utils.isValidUserName(request.getUserName())){
            errorMessage = ErrorConstants.userNameInvalidError;

        //Verfying mobileNo
        } else if (!Utils.isValidMobileNo(request.getMobileNo())) {
            errorMessage = ErrorConstants.mobileNoInvalidError;

        //Verifying outlet
        } else if (!Utils.isValidOutlet(request.getOutlet())) {
            errorMessage = ErrorConstants.outletInvalidError;

        //verifying date
        } else if (!Utils.isValidDate(request.getDate())) {
            errorMessage = ErrorConstants.dateInvalidError;

        //verifying time
        } else if (!Utils.isValidtime(request.getTime())) {
            errorMessage = ErrorConstants.timeInvalidError;

        //verifying count
        } else if (!Utils.isValidCount(request.getCount())) {
            errorMessage = ErrorConstants.countInvalidError;
        }
        if(!errorMessage.equals("")){
            return ResponseEntity.badRequest().body(new ApiResponse<>().badRequestResponse(errorMessage));
        }


        ApiResponse<GenericResponse> response = reservationRequestService.makeReservation(request);
        return ResponseEntity.ok().body(response);
    }

    @GetMapping
    public ResponseEntity<List<ReservationEntity>> getAllReservation(@RequestParam(required = false) String search,@RequestParam(required = false) Integer limit, @RequestParam(required = false) Integer skip){
        return new ResponseEntity<List<ReservationEntity>>(reservationRequestService.getAllReservation(search, limit,skip), HttpStatus.OK);
    }



    @GetMapping("/{id}")
    public ResponseEntity<Optional<ReservationEntity>> getReservationById(@PathVariable int id){
        return new ResponseEntity<Optional<ReservationEntity>>(reservationRequestService.getReservationById(id), HttpStatus.OK);
    }

    @DeleteMapping("/{id}")
    public ResponseEntity<?> deleteReservation(@PathVariable int id){
        ApiResponse<GenericResponse> response =  reservationRequestService.deleteReservation(id);
        return ResponseEntity.ok().body(response);

    }
    @PutMapping("/{id}")
    public ResponseEntity<?> updateReservation(@PathVariable int id,@RequestBody ReservationRequestDto request){
        ApiResponse<GenericResponse> response = reservationRequestService.updateReservation(id,request);
        return ResponseEntity.ok().body(response);

    }


}
