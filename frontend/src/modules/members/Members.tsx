import { Routes, Route } from "react-router-dom";
import { MembersList } from "./MembersList";
import { MemberProfile } from "./MemberProfile";

export default function MembersModule() {
  return (
    <Routes>
      <Route index element={<MembersList />} />
      <Route path=":id" element={<MemberProfile />} />
    </Routes>
  );
}
