package com.accelerate.acceleronServices.reservation.service;

import com.accelerate.acceleronServices.reservation.dto.request.ReservationRequestDto;
import com.accelerate.acceleronServices.reservation.dto.response.ApiResponse;
import com.accelerate.acceleronServices.reservation.dto.response.GenericResponse;
import com.accelerate.acceleronServices.reservation.model.ReservationEntity;
import com.accelerate.acceleronServices.reservation.model.ReservationSummary;


import java.util.List;

public interface ReservationRequestService {
    ApiResponse<GenericResponse> makeReservation(ReservationRequestDto request);

    List<ReservationEntity> getAllReservation(Integer limit, Integer skip);

    ReservationEntity getReservationById(int id);

    ApiResponse<GenericResponse> deleteReservation(int id);

    ApiResponse<GenericResponse> updateReservation(int id, ReservationRequestDto request);


    List<ReservationEntity> getAllReservationBySearch(String search, Integer limit);

    ApiResponse<GenericResponse> updateStatus(int id, String status);

    ReservationSummary getReservationSummaryByUserId(String userId);

    ReservationSummary getReservationSummaryPerBranch(String outlet, String date);


}
